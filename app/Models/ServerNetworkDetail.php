<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerNetworkDetail extends Model
{
    use HasFactory;

    public function vpnServer(): BelongsTo
    {
        return $this->belongsTo(VpnServer::class);
    }
}
