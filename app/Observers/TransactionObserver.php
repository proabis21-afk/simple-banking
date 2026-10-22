<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionObserver
{
    public function creating(Transaction $transaction): void
    {
        if (!in_array(
            $transaction->type,
            ['deposit', 'withdrawal', 'transfer'],
            true
        )) {
            throw ValidationException::withMessages([
                'type' => 'Invalid transaction type.',
            ]);
        }

        if (Money::cents((string) $transaction->amount) < 100) {
            throw ValidationException::withMessages([
                'amount' => 'The minimum amount is ₱1.00.',
            ]);
        }
    }

    public function created(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($transaction->account_id);

            $balance = Money::cents($account->balance);
            $amount = Money::cents($transaction->amount);
            $minimum = Money::cents($account->minimum_balance);

            $newBalance = $transaction->type === 'deposit'
                ? $balance + $amount
                : $balance - $amount;

            if (
                $newBalance < 0
                || (
                    $transaction->type !== 'deposit'
                    && $newBalance < $minimum
                )
            ) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds. Maintain at least ₱'
                        . $account->minimum_balance
                        . ' in your account.',
                ]);
            }

            if ($newBalance > 99999999999999) {
                throw ValidationException::withMessages([
                    'amount' => 'Account balance limit exceeded.',
                ]);
            }

            $account->balance = Money::decimal($newBalance);
            $account->save();
        });
    }
}