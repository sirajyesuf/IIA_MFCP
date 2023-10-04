<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];


    public function payments(){
    
        return $this->hasMany(Payment::class);
        
    }

    // /**
    //  * Get the plage's most recent payment.
    //  */
    // public function latestPayment(): HasOne
    // {
    //     return $this->hasOne(Payment::class)->latestOfMany();
    // }

}
