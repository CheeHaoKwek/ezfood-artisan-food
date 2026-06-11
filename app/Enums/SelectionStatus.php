<?php

namespace App\Enums;

enum SelectionStatus: string
{
    case Selected = 'selected';
    case Locked = 'locked';
    case Cancelled = 'cancelled';
}
