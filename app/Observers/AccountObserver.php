<?php

namespace App\Observers;

use App\Models\Account;
use App\Notifications\LowBalanceNotification;
use App\Support\Money;

class AccountObserver
{
    public function updated(Account $account): void
    {
        if (!$account->wasChanged('balance')) {
            return;
        }

        $previousBalance = Money::cents(
            (string) $account->getRawOriginal('balance')
        );

        $currentBalance = Money::cents($account->balance);

        $minimumBalance = Money::cents(
            $account->minimum_balance
        );

        if (
            $currentBalance < $previousBalance
            && $previousBalance > $minimumBalance
            && $currentBalance <= $minimumBalance
        ) {
            $account->user->notify(
                new LowBalanceNotification($account)
            );
        }
    }
}