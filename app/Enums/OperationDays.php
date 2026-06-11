<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum OperationDays: string
{
    case MonToFri = 'mon_fri';
    case MonToSat = 'mon_sat';
    case MonToSun = 'mon_sun';

    public function includes(CarbonInterface $date): bool
    {
        return match ($this) {
            self::MonToFri => $date->isWeekday(),
            self::MonToSat => ! $date->isSunday(),
            self::MonToSun => true,
        };
    }
}
