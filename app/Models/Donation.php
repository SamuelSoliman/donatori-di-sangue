<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\DonationsScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
class Donation extends Model
{
    public $timestamps = false;

    //#[ScopedBy([DonationsScope::class])]
    protected static function booted()
    {
        static::addGlobalScope(new DonationsScope);
    }
    public function doner(): BelongsTo
    {
        return $this->belongsTo(Doner::class, 'doner_email', 'email');
    }
    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class,"location","center");
    }
    // public function getCountOfUsersPerCenterAttribute()
    // {
    //     return User::where('Center', '=', $this->location)->count();
    // }
}
