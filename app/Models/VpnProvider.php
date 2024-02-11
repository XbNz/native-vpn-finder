<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VpnProvider extends Model
{
    use HasFactory;

    public function vpnServers(): HasMany
    {
        return $this->hasMany(VpnServer::class);
    }
}
