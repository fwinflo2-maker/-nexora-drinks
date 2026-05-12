<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Loaded = 'loaded';
    case Delivered = 'delivered';
    case Invoiced = 'invoiced';
    case Cancelled = 'cancelled';
}
