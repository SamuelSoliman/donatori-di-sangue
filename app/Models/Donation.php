<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\DonationsScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
class Donation extends Model
{
    public $timestamps = false;
    // protected $fillable = [
    //     'doner_email',
    //     'donation_date',
    //     'center',
    // ];
    protected $guarded = [];
    //#[ScopedBy([DonationsScope::class])]
    protected static function booted()
    {
        static::addGlobalScope(new DonationsScope);
    }
    public function donerRelation(): BelongsTo
    {
        return $this->belongsTo(Doner::class, 'doner_email', 'email');
    }
    public function centerRelation(): BelongsTo
    {
        return $this->belongsTo(Center::class,"center","location");
    }
    // public function getCountOfUsersPerCenterAttribute()
    // {
    //     return User::where('Center', '=', $this->location)->count();
    // }
}
