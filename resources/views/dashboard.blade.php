<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Banking Dashboard</title>
</head>
<body>
    <h1>Banking Dashboard</h1>

    <p>
        Welcome, {{ auth()->user()->name }}!
    </p>

    <p>
        <a href="{{ route('transactions.create') }}">
            New Transaction
        </a>

        |

        <a href="{{ route('transactions.index') }}">
            View Transactions
        </a>
    </p>

    <h2>Account Statistics</h2>

    <p>
        Total accounts:
        <strong id="total-accounts">Loading...</strong>
    </p>

    <p>
        Total money in your accounts:
        <strong id="total-money">Loading...</strong>
    </p>

    <p>
        Total transactions today:
        <strong id="transactions-today">Loading...</strong>
    </p>

    <p>
        Updates automatically every 30 seconds.
    </p>

    <p id="update-status" role="status"></p>

    <h2>My Accounts</h2>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Account Number</th>
                <th>Balance</th>
                <th>Minimum Balance</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($accounts as $account)
                <tr>
                    <td>
                        {{ $account->account_number }}
                    </td>

                    <td id="balance-{{ $account->id }}">
                        ₱{{ number_format(
                            (float) $account->balance,
                            2
                        ) }}
                    </td>

                    <td>
                        ₱{{ number_format(
                            (float) $account->minimum_balance,
                            2
                        ) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        No accounts found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Low Balance Notifications</h2>

    <div id="notifications">
        Loading notifications...
    </div>

    <br>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">Logout</button>
    </form>

    <script>
        const statsUrl = @json(route('dashboard.stats'));

        const currency = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        });

        let loading = false;

        async function updateDashboard() {
            if (loading) {
                return;
            }

            loading = true;

            const controller = new AbortController();

            const timeout = setTimeout(() => {
                controller.abort();
            }, 10000);

            try {
                const response = await fetch(statsUrl, {
                    credentials: 'same-origin',

                    headers: {
                        'Accept': 'application/json'
                    },

                    signal: controller.signal
                });

                if (!response.ok) {
                    throw new Error('Unable to load dashboard.');
                }

                const data = await response.json();

                document.getElementById(
                    'total-accounts'
                ).textContent = data.total_accounts;

                document.getElementById(
                    'total-money'
                ).textContent = currency.format(
                    data.total_money_in_bank
                );

                document.getElementById(
                    'transactions-today'
                ).textContent = data.total_transactions_today;

                for (const account of data.accounts) {
                    const balanceElement = document.getElementById(
                        'balance-' + account.id
                    );

                    if (balanceElement) {
                        balanceElement.textContent = currency.format(
                            account.balance
                        );
                    }
                }

                const notificationContainer =
                    document.getElementById('notifications');

                notificationContainer.replaceChildren();

                if (data.notifications.length === 0) {
                    const paragraph = document.createElement('p');

                    paragraph.textContent =
                        'No low balance notifications.';

                    notificationContainer.appendChild(paragraph);
                }

                for (const notification of data.notifications) {
                    const paragraph = document.createElement('p');

                    paragraph.textContent =
                        notification.account_number
                        + ' — '
                        + notification.message
                        + ' Balance: '
                        + currency.format(notification.balance);

                    notificationContainer.appendChild(paragraph);
                }

                document.getElementById(
                    'update-status'
                ).textContent =
                    'Last updated: '
                    + new Date().toLocaleTimeString();

            } catch (error) {
                document.getElementById(
                    'update-status'
                ).textContent =
                    'Unable to update. Retrying in 30 seconds.';

            } finally {
                clearTimeout(timeout);
                loading = false;
            }
        }

        updateDashboard();

        setInterval(updateDashboard, 30000);
    </script>
</body>
</html>