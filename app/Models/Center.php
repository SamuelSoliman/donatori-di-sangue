<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['location'];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function donations() {
        return $this->hasMany(Donation::class,"center","location");
    }
}
