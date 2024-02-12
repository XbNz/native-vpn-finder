<?php

namespace App\Actions;

use App\Enums\Protocol;
use App\Models\City;
use App\Models\Country;
use App\Models\ServerNetworkDetail;
use App\Models\VpnProvider;
use App\Models\VpnServer;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Throwable;
use Psl\Type;

class RefreshVpnServersAction
{
    public function __construct(
        private readonly DatabaseManager $database,
    ) {
    }

    public function handle(): void
    {
        $this->database->beginTransaction();

        try {
            ServerNetworkDetail::query()->truncate();
            VpnServer::query()->truncate();

            $serversRaw = Http::get('https://raw.githubusercontent.com/qdm12/gluetun/master/internal/storage/servers.json')
                ->throw()
                ->json();

            $serversWithCountryAndLegitIps = Collection::make($serversRaw)
                ->forget('version')
                ->map(function (array $vpnService) {
                    return Collection::make($vpnService['servers'])
                        ->filter(function (array $server) {
                            $hasCountry = array_key_exists('country', $server) && $server['country'] !== '';
                            $hasBadIpAddresses = Collection::make($server['ips'])
                                    ->filter(fn (string $ip) => filter_var($ip, FILTER_VALIDATE_IP) === false)
                                    ->isNotEmpty();

                            if ($hasCountry === true && $hasBadIpAddresses === false) {
                                return true;
                            }

                            return false;
                        })->toArray();
                })->toArray();

            $servers = Type\non_empty_dict(
                Type\string(),
                Type\vec(Type\shape([
                    'vpn' => Type\optional(Type\backed_enum(Protocol::class)),
                    'country' => Type\string(),
                    'region' => Type\optional(Type\string()),
                    'city' => Type\optional(Type\string()),
                    'hostname' => Type\optional(Type\string()),
                    'ips' => Type\non_empty_vec(Type\non_empty_string()),
                ]))
            )->coerce($serversWithCountryAndLegitIps);

            foreach ($servers as $providerName => $providerServers) {
                foreach ($providerServers as $server) {
                    foreach ($server['ips'] as $ip) {
                        ServerNetworkDetail::query()->create([
                            'ip_address' => $ip,
                            'hostname' => $server['hostname'] ?? null,
                            'vpn_server_id' => VpnServer::query()->create([
                                'vpn_provider_id' => VpnProvider::query()->firstOrCreate(['name' => $providerName])->id,
                                'country_id' => Country::query()->firstOrCreate(['name' => $server['country']])->id,
                                'city_id' => array_key_exists('city', $server)
                                    ? City::query()->firstOrCreate([
                                        'name' => $server['city'],
                                        'country_id' => Country::query()->where('name', $server['country'])->value('id'),
                                    ])->id
                                    : null,
                                'protocol' => $server['vpn'] ?? null,
                            ])->id,
                        ]);
                    }
                }
            }

            $this->database->commit();
        } catch (Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }
    }
}
