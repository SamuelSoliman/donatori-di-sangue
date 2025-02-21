<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doner extends Model
{
    //
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'doner_email', 'email');
    }
    
}
