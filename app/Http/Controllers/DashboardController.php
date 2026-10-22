<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where(
            'user_id',
            $request->user()->id
        )->get();

        return view('dashboard', compact('accounts'));
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $accounts = Account::where('user_id', $userId);

        $totalTransactionsToday = Transaction::whereHas(
            'account',
            function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        )
            ->whereBetween('created_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])
            ->count();

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'account_number' => $notification->data['account_number'],
                    'message' => $notification->data['message'],
                    'balance' => $notification->data['balance'],
                ];
            });

        return response()->json([
            'total_accounts' => (clone $accounts)->count(),

            'total_money_in_bank' => (string) (
                (clone $accounts)->sum('balance')
            ),

            'total_transactions_today' => $totalTransactionsToday,

            'accounts' => (clone $accounts)->get([
                'id',
                'balance',
            ]),

            'notifications' => $notifications,
        ])->header('Cache-Control', 'no-store');
    }
}