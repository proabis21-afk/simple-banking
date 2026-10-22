<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class Money
{
    public static function cents(string $value): int
    {
        if (!preg_match('/^\d{1,12}(?:\.\d{1,2})?$/D', $value)) {
            throw ValidationException::withMessages([
                'amount' => 'Enter a valid amount with up to two decimal places.',
            ]);
        }

        [$whole, $fraction] = array_pad(
            explode('.', $value),
            2,
            ''
        );

        return ((int) $whole * 100)
            + (int) str_pad($fraction, 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100)
            . '.'
            . str_pad(
                (string) ($cents % 100),
                2,
                '0',
                STR_PAD_LEFT
            );
    }
}