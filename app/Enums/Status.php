<?php 
namespace App\Enums;
use App\Enums\EnumToArray;

enum Status:int
{
    use EnumToArray;

    case ACTIVE = 1;
    case DEACTIVATE  = 0;
}