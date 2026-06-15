<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MealSlotType: string implements HasLabel
{
    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
