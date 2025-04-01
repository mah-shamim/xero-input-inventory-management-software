<?php

namespace App\Enums;

enum PaymentTypeEnum: int
{
    case Cash = 1;
    case CreditCard = 2;
    case Cheque = 3;
}
