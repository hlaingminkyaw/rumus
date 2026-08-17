@extends(config('composer-rumus.layout', 'composer-rumus::layouts.report'))

@section(config('composer-rumus.layout_section', 'content'))
<h1>Invoice Report</h1>
<form method="get" action="{{ route('composer-rumus.invoice.search') }}"><table class="filter"><tr><td><label>From <input type="date" name="start_date" value="{{ $startDate }}" required></label></td><td><label>To <input type="date" name="end_date" value="{{ $endDate }}" required></label></td><td><label>Location <select name="branch"><option value="">All permitted locations</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string) $branch === (string) $warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></label></td><td><button>Search</button></td></tr></table></form>
<table><thead><tr><th>#</th><th>Invoice</th><th>Location</th><th>Customer</th><th>Phone</th><th>Date</th><th>Created by</th><th>Total</th></tr></thead><tbody>
@forelse($invoices as $invoice)<tr><td>{{ $loop->iteration }}</td><td>{{ $invoice->invoice_no }}</td><td>{{ $invoice->warehouse->name ?? $invoice->branch }}</td><td>{{ $invoice->customer->name ?? $invoice->customer_name ?? '-' }}</td><td>{{ $invoice->customer->phno ?? $invoice->phno ?? '-' }}</td><td>{{ $invoice->invoice_date ?? $invoice->created_at?->toDateString() }}</td><td>{{ $invoice->creator->name ?? '-' }}</td><td>{{ number_format($invoice->total, 2) }}</td></tr>@empty<tr><td colspan="8">No invoices found.</td></tr>@endforelse
</tbody><tfoot><tr class="total"><td colspan="7">Total</td><td>{{ number_format($total, 2) }}</td></tr></tfoot></table>
@endsection
