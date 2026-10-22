<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Transaction History</title>
</head>
<body>
    <h1>Transaction History</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    <p>
        <a href="{{ route('transactions.create') }}">
            New Transaction
        </a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Reference Number</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>
                        {{ $transaction->created_at->format('M d, Y h:i A') }}
                    </td>

                    <td>
                        {{ $transaction->account->account_number }}
                    </td>

                    <td>
                        {{ ucfirst($transaction->type) }}
                    </td>

                    <td>
                        {{ number_format((float) $transaction->amount, 2) }}
                    </td>

                    <td>
                        {{ $transaction->reference_number ?? '—' }}
                    </td>

                    <td>
                        {{ $transaction->notes ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        No transactions found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>
        @if ($transactions->previousPageUrl())
            <a href="{{ $transactions->previousPageUrl() }}">
                Previous
            </a>
        @endif

        Page {{ $transactions->currentPage() }}
        of {{ $transactions->lastPage() }}

        @if ($transactions->nextPageUrl())
            <a href="{{ $transactions->nextPageUrl() }}">
                Next
            </a>
        @endif
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">Logout</button>
    </form>
</body>
</html>