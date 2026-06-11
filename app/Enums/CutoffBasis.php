<?php

namespace App\Enums;

enum CutoffBasis: string
{
    case PerSlot = 'per_slot';
    case PerDay = 'per_day';
}
