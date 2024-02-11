<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VpnServer extends Model
{
    use HasFactory;

    public function vpnProvider(): BelongsTo
    {
        return $this->belongsTo(VpnProvider::class);
    }
}
