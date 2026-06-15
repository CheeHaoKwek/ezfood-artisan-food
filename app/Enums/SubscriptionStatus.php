<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasLabel
{
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
