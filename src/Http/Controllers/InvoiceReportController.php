<?php

namespace ComposerRumus\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceReportController extends Controller
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

    private function render(Request $request, Carbon $start, Carbon $end)
    {
        $columns = config('composer-rumus.columns');
        $invoiceClass = config('composer-rumus.models.invoice');
        $branch = $request->input('branch');
        $query = $invoiceClass::query()
            ->whereBetween($columns['invoice_created_at'], [$start, $end])
            ->where($columns['invoice_status'], config('composer-rumus.invoice_status_value'))
            ->where($columns['invoice_balance_due'], config('composer-rumus.invoice_balance_due_value'));

        $this->applyBranchScope($query, $request, $branch);
        $invoices = $query->with(['customer', 'creator', 'warehouse'])->orderByDesc($columns['invoice_created_at'])->get();
        $warehouses = $this->warehouses();

        return view('composer-rumus::reports.invoice', [
            'invoices' => $invoices,
            'total' => $invoices->sum($columns['invoice_total']),
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'branch' => $branch,
            'warehouses' => $warehouses,
            'isAdmin' => $this->isAdmin($request),
        ]);
    }

    private function applyBranchScope($query, Request $request, mixed $branch): void
    {
        $column = config('composer-rumus.columns.invoice_branch');
        if ($this->isAdmin($request)) {
            if ($branch !== null && $branch !== '') $query->where($column, $branch);
            return;
        }

        $permitted = $this->permittedBranches($request);
        $query->whereIn($column, $permitted ?: [-1]);
        if ($branch !== null && $branch !== '') $query->where($column, $branch);
    }

    private function warehouses()
    {
        $class = config('composer-rumus.models.warehouse');
        return $class::query()->orderBy('name')->get();
    }

    private function isAdmin(Request $request): bool
    {
        $user = $request->user();
        return (string) data_get($user, config('composer-rumus.admin_flag_field')) === config('composer-rumus.admin_flag_value')
            || (string) data_get($user, config('composer-rumus.permission_field')) === config('composer-rumus.admin_permission_value');
    }

    private function permittedBranches(Request $request): array
    {
        $branches = json_decode((string) data_get($request->user(), config('composer-rumus.permission_field'), '[]'), true);
        return is_array($branches) ? $branches : [];
    }
}
