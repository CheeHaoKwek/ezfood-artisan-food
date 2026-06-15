<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConsolidatedOrderStatus: string implements HasLabel
{
    case Pending = 'pending';
    case SentToLogistics = 'sent_to_logistics';
    case Delivered = 'delivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::SentToLogistics => 'Sent to logistics',
            self::Delivered => 'Delivered',
        };
    }
}
