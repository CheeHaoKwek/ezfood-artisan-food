<?php

namespace App\Enums;

enum MealMode: string
{
    case SinglePerDay = 'single';
    case MultiplePerDay = 'multiple';
}
