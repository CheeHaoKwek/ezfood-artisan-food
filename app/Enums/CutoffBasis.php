<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CutoffBasis: string implements HasLabel
{
    case PerSlot = 'per_slot';
    case PerDay = 'per_day';

    public function getLabel(): string
    {
        return match ($this) {
            self::PerSlot => 'Per slot',
            self::PerDay => 'Per day',
        };
    }
}
