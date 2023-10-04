<?php 
namespace App\Enums;
use App\Enums\EnumToArray;

enum Gender:string
{
    use EnumToArray;

    case MALE = "Male";
    case FEMALE =  "Female";
}