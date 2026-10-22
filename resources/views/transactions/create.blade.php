<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>New Transaction</title>
</head>
<body>
    <h1>New Transaction</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
     <form
        method="POST"
        action="{{ route('transactions.store') }}"
    >
        @csrf

        <label for="account_id">Account</label>
        <br>

        <select id="account_id" name="account_id" required>
            @foreach ($accounts as $account)
                <option
                    value="{{ $account->id }}"
                    @selected(old('account_id') == $account->id)
                >
                    {{ $account->account_number }}
                    (Balance: {{ number_format(
                        (float) $account->balance,
                        2
                    ) }})
                </option>
            @endforeach
        </select>

        <br><br>

        <label for="type">Type</label>
        <br>

        <select id="type" name="type" required>
            <option
                value="withdrawal"
                @selected(old('type', 'withdrawal') === 'withdrawal')
            >
                Withdrawal
            </option>

            <option
                value="deposit"
                @selected(old('type') === 'deposit')
            >
                Deposit
            </option>
        </select>

        <br><br>

        <label for="amount">Amount</label>
        <br>

        <input
            id="amount"
            type="number"
            name="amount"
            min="1"
            max="9999999999.99"
            step="0.01"
            value="{{ old('amount') }}"
            required
        >

        <br><br>

        <label for="reference_number">Reference number</label>
        <br>

        <input
            id="reference_number"
            type="text"
            name="reference_number"
            maxlength="255"
            value="{{ old('reference_number') }}"
        >

        <br><br>

        <label for="notes">Notes</label>
        <br>

        <textarea
            id="notes"
            name="notes"
            rows="2"
            cols="20"
            maxlength="1000"
        >{{ old('notes') }}</textarea>

        <br><br>

        <button type="submit">Submit</button>
    </form>

    <p>
        <a href="{{ route('transactions.index') }}">
            View transactions
        </a>
    </p>
</body>
</html>
