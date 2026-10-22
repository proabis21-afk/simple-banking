<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            [
                'email' => 'student@example.com',
            ],
            [
                'name' => 'Student User',
                'password' => Hash::make('Banking123!'),
            ]
        );

        $account = Account::firstOrCreate(
            [
                'account_number' => 'ACC-0001',
            ],
            [
                'user_id' => $user->id,
                'balance' => '0.00',
                'minimum_balance' => '100.00',
            ]
        );

        if (!$account->transactions()->exists()) {
            Transaction::create([
                'account_id' => $account->id,
                'type' => 'deposit',
                'amount' => '1000.00',
                'reference_number' => (string) Str::uuid(),
                'notes' => 'Opening deposit.',
            ]);
        }
    }
}