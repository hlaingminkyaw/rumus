<?php

namespace ComposerRumus\Http\Controllers;

use Carbon\Carbon;
use ComposerRumus\Support\HostModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceCashReportController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request, Carbon::today(), Carbon::today());
    }

    public function search(Request $request)
    {
        $request->validate(['start_date' => ['required', 'date'], 'end_date' => ['required', 'date']]);

        return $this->render(
            $request,
            Carbon::parse($request->string('start_date'))->startOfDay(),
            Carbon::parse($request->string('end_date'))->endOfDay(),
        );
    }

    public function expenseDetail(Request $request, string $method)
    {
        $start = Carbon::parse($request->input('start_date', today()))->startOfDay();
        $end = Carbon::parse($request->input('end_date', $start))->endOfDay();
        $expenses = $this->expenses($start, $end)->filter(fn ($expense) => $this->bucket($expense->transaction->transaction_name ?? $expense->name ?? '') === $method);

        return view('composer-rumus::reports.expense-detail', compact('expenses', 'method', 'start', 'end'));
    }

    private function render(Request $request, Carbon $start, Carbon $end)
    {
        $columns = config('composer-rumus.columns');
        $paymentClass = HostModel::resolve('payment');
        $invoiceRelation = HostModel::relation('payment_invoice');
        $transactionRelation = HostModel::relation('payment_transaction');

        $query = $paymentClass::query()
            ->whereBetween($columns['payment_date'], [$start, $end])
            ->where(function ($query) use ($columns, $transactionRelation) {
                $query->where($columns['payment_amount'], '>', 0)
                    ->orWhere($columns['payment_method'], 'Credit');

                // Only applies when the host payment model exposes a transaction relation.
                if ($transactionRelation !== null) {
                    $query->orWhereHas($transactionRelation, fn ($transaction) => $transaction->whereIn('transaction_name', ['Credit', 'INV-Credit']));
                }
            });

        if ($invoiceRelation !== null) {
            $query->whereHas($invoiceRelation, function ($invoice) use ($columns, $request) {
                $invoice->where($columns['invoice_status'], config('composer-rumus.invoice_status_value'))
                    ->where($columns['invoice_balance_due'], config('composer-rumus.invoice_balance_due_value'));
                $this->applyBranchScope($invoice, $request);
            });
        }

        $payments = $query
            ->with(HostModel::eagerLoad('payment_transaction', 'payment_customer'))
            ->orderByDesc($columns['payment_date'])
            ->get();

        $summary = $this->paymentSummary($payments);
        $expenses = $this->expenses($start, $end);
        $expenseSummary = $expenses->groupBy(fn ($expense) => $this->bucket($expense->transaction->transaction_name ?? $expense->name ?? ''))
            ->map(fn ($rows) => ['total' => $rows->sum($columns['expense_amount']), 'count' => $rows->count()]);
        $profitSummary = collect(['Cash', 'Kpay', 'MMQR', 'Credit', 'Mobile Banking'])->mapWithKeys(function ($method) use ($summary, $expenseSummary) {
            $income = $summary[$method]['total'] ?? 0;
            $expense = $expenseSummary[$method]['total'] ?? 0;
            return [$method => ['income_total' => $income, 'income_count' => $summary[$method]['count'] ?? 0, 'expense_total' => $expense, 'profit' => $income - $expense]];
        });

        return view('composer-rumus::reports.cash', compact('payments', 'summary', 'expenses', 'expenseSummary', 'profitSummary', 'start', 'end'));
    }

    private function paymentSummary($payments): array
    {
        $amount = config('composer-rumus.columns.payment_amount');
        return $payments->groupBy(fn ($payment) => $this->bucket($payment->transaction->transaction_name ?? $payment->payment_method ?? ''))
            ->map(function ($rows) use ($amount) {
                $total = $rows->sum(function ($payment) use ($amount) {
                    $value = (float) $payment->{$amount};
                    return $this->bucket($payment->transaction->transaction_name ?? '') === 'Credit' && $value <= 0
                        ? (float) ($payment->invoice->remain_balance ?? 0) : $value;
                });
                return ['total' => $total, 'count' => $rows->count()];
            })->all();
    }

    private function expenses(Carbon $start, Carbon $end)
    {
        $class = HostModel::resolve('expense');
        return $class::query()->whereBetween(config('composer-rumus.columns.expense_date'), [$start, $end])
            ->with(HostModel::eagerLoad('expense_transaction', 'expense_warehouse'))->get();
    }

    private function applyBranchScope($query, Request $request): void
    {
        if ($this->isAdmin($request)) return;
        $branches = json_decode((string) data_get($request->user(), config('composer-rumus.permission_field'), '[]'), true);
        $permitted = is_array($branches) ? $branches : [];
        $query->whereIn(config('composer-rumus.columns.invoice_branch'), $permitted ?: [-1]);
    }

    private function isAdmin(Request $request): bool
    {
        $user = $request->user();
        return (string) data_get($user, config('composer-rumus.admin_flag_field')) === config('composer-rumus.admin_flag_value')
            || (string) data_get($user, config('composer-rumus.permission_field')) === config('composer-rumus.admin_permission_value');
    }

    private function bucket(string $name): string
    {
        return match (strtolower(trim($name))) {
            '', 'cash', 'inv-cash', 'exp-cash' => 'Cash',
            'kpay', 'k pay', 'k pay 2', 'inv-kpay' => 'Kpay',
            'mmqr' => 'MMQR',
            'credit', 'inv-credit' => 'Credit',
            default => 'Mobile Banking',
        };
    }
}
