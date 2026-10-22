<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('account')
            ->whereHas('account', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->latest('id')
            ->paginate(10);

        return view(
            'transactions.index',
            compact('transactions')
        );
    }

    public function create(Request $request)
    {
        $accounts = Account::where(
            'user_id',
            $request->user()->id
        )->get();

        return view(
            'transactions.create',
            compact('accounts')
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(
                    'user_id',
                    $request->user()->id
                ),
            ],

            'type' => [
                'required',
                Rule::in(['deposit', 'withdrawal']),
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:9999999999.99',
                'regex:/^\d{1,10}(?:\.\d{1,2})?$/D',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($request->input('type') !== 'withdrawal') {
                return;
            }

            $account = Account::where(
                'user_id',
                $request->user()->id
            )->find($request->input('account_id'));

            if (!$account) {
                return;
            }

            $remaining = Money::cents($account->balance)
                - Money::cents((string) $request->input('amount'));

            if ($remaining < Money::cents($account->minimum_balance)) {
                $validator->errors()->add(
                    'amount',
                    'Insufficient funds. Maintain at least ₱'
                    . $account->minimum_balance
                    . ' in your account.'
                );
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($validated, $request) {
            Account::where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->findOrFail($validated['account_id']);

            Transaction::create([
                'account_id' => $validated['account_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'reference_number' => $validated['reference_number']
                    ?? (string) Str::uuid(),
                'notes' => $validated['notes'] ?? null,
            ]);
        }, 3);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction completed successfully.');
    }
}