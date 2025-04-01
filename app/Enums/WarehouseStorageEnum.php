<?php

namespace App\Enums;

enum WarehouseStorageEnum: string
{
    case Storage = 'storage';
    case Staging = 'staging';
    case Both = 'both';
}
