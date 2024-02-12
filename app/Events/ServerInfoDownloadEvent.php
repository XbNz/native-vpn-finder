<?php

namespace App\Events;

use App\Enums\DownloadType;
use App\Models\ServerNetworkDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;

class ServerInfoDownloadEvent
{
    /**
     * @param Collection<int, ServerNetworkDetail> $serverNetworkDetails
     */
    public function __construct(
        public readonly Collection $serverNetworkDetails,
        public readonly DownloadType $downloadType,
    ) {
    }
}
