<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DietaryPreference: string implements HasLabel
{
    case Vegetarian = 'vegetarian';
    case NonVegetarian = 'non_vegetarian';

    public function getLabel(): string
    {
        return match ($this) {
            self::Vegetarian => 'Vegetarian',
            self::NonVegetarian => 'Non-vegetarian',
        };
    }
}
