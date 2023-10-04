<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function  plages(){

        return $this->hasMany(Plage::class);
    }

    public function ActivePlage(){

        return $this->plages()->where('status',1);
    }

}
