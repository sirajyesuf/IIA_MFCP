<?php
namespace  App\Services;

use App\Models\Member;

abstract class MemberService
{
    public static function  IDGenerator()
    {
        $unique = false;

        while(!$unique)
        {
            $memberID =  "IIA".rand(10000,99999);
            $existingMemberID  = Member::where('member_id',$memberID)->first();
            $unique = ! $existingMemberID;
        }

        return $memberID;
    } 
}