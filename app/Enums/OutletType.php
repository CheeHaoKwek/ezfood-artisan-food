<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OutletType: string implements HasLabel
{
    case Factory = 'factory';
    case Condo = 'condo';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
