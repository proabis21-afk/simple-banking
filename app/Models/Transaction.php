<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use LogicException;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function save(array $options = [])
    {
        return DB::transaction(
            fn () => parent::save($options)
        );
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException(
                'Posted transactions cannot be edited.'
            );
        });

        static::deleting(function () {
            throw new LogicException(
                'Posted transactions cannot be deleted.'
            );
        });
    }
}