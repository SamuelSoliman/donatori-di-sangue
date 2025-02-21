<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    //
    public function doner(): BelongsTo
    {
        return $this->belongsTo(Doner::class, 'doner_email', 'email');
    }
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }
}
