<?php

namespace App\DTOs;

use App\Enums\Protocol;
use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\ServerNetworkDetail;
use App\Models\VpnProvider;
use Illuminate\Support\Collection;

class VpnServerDTO
{
    /**
     * @param Protocol $protocol
     * @param VpnProvider $vpnProvider
     * @param Country $country
     * @param Region|null $region
     * @param City|null $city
     */
    public function __construct(
        public readonly string $ipAddress,
        public readonly int $ipVersion,
        public readonly VpnProvider $vpnProvider,
        public readonly Country $country,
        public readonly ?string $hostname,
        public readonly ?Region $region,
        public readonly ?City $city,
        public readonly ?Protocol $protocol,
    ) {
    }
}
