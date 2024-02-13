<?php

namespace App\Models;

use App\Enums\Protocol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VpnServer extends Model
{
    use HasFactory;

    protected $casts = [
        'protocol' => Protocol::class,
    ];

    public function vpnProvider(): BelongsTo
    {
        return $this->belongsTo(VpnProvider::class);
    }

    public function serverNetworkDetails(): HasMany
    {
        return $this->hasMany(ServerNetworkDetail::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
