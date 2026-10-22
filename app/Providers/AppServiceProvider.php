<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Transaction;
use App\Observers\AccountObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Account::observe(AccountObserver::class);

        Transaction::observe(TransactionObserver::class);
    }
}