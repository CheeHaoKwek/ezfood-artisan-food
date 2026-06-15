<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MealMode: string implements HasLabel
{
    case SinglePerDay = 'single';
    case MultiplePerDay = 'multiple';

    public function getLabel(): string
    {
        return match ($this) {
            self::SinglePerDay => '1 meal/day',
            self::MultiplePerDay => 'Multiple meals/day',
        };
    }
}
