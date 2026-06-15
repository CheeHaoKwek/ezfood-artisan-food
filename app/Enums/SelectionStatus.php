<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SelectionStatus: string implements HasLabel
{
    case Selected = 'selected';
    case Locked = 'locked';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
