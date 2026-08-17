@extends(config('composer-rumus.layout', 'composer-rumus::layouts.report'))

@section(config('composer-rumus.layout_section', 'content'))
<p><a href="{{ route('composer-rumus.cash.search', ['start_date' => $start->toDateString(), 'end_date' => $end->toDateString()]) }}">← Invoice Cash Report</a></p>
<h1>{{ $method }} expenses</h1>
<table>
    <thead><tr><th>#</th><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
    <tbody>
        @forelse($expenses as $expense)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $expense->date }}</td>
                <td>{{ $expense->name ?? '-' }}</td>
                <td>{{ number_format($expense->amount_mmk, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No expenses found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
