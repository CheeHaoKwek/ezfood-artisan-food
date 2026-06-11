<?php

namespace App\Enums;

enum ConsolidatedOrderStatus: string
{
    case Pending = 'pending';
    case SentToLogistics = 'sent_to_logistics';
    case Delivered = 'delivered';
}
