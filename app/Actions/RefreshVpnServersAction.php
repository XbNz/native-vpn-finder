<?php

namespace App\Actions;

use App\DTOs\VpnServerDTO;
use App\Enums\Protocol;
use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use App\Models\VpnProvider;
use App\Models\VpnServer;
use Filament\Notifications\Notification;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
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
        VpnServer::query()->truncate();
        VpnProvider::query()->truncate();
        Region::query()->truncate();
        Country::query()->truncate();
        City::query()->truncate();

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

        LazyCollection::make($servers)
            ->each($this->createRegions(...))
            ->each($this->createCountries(...))
            ->each($this->createCities(...))
            ->each($this->createProviders(...))
            ->each($this->createVpnServers(...));
    }

    private function createRegions(array $servers, string $providerName): void
    {
        $regions = Arr::pluck($servers, 'region');
        $regions = array_filter($regions);
        $regions = array_unique($regions);
        array_walk($regions, fn (&$region) => $region = ['name' => $region]);
        array_walk($regions, fn (&$region) => $region = Arr::add($region, 'created_at', now()));
        array_walk($regions, fn (&$region) => $region = Arr::add($region, 'updated_at', now()));
        $this->database->table('regions')->insertOrIgnore($regions);
    }

    private function createCountries(array $servers, string $providerName): void
    {
        $countries = Arr::pluck($servers, 'country');
        $countries = array_unique($countries);
        array_walk($countries, fn (&$country) => $country = ['name' => $country]);
        array_walk($countries, fn (&$country) => $country = Arr::add($country, 'created_at', now()));
        array_walk($countries, fn (&$country) => $country = Arr::add($country, 'updated_at', now()));
        $this->database->table('countries')->insertOrIgnore($countries);
    }

    private function createCities(array $servers, string $providerName): void
    {
        $cities = Arr::pluck($servers, 'city');
        $cities = array_filter($cities);
        $cities = array_unique($cities);
        array_walk($cities, fn (&$city) => $city = ['name' => $city]);
        array_walk($cities, fn (&$city) => $city = Arr::add($city, 'created_at', now()));
        array_walk($cities, fn (&$city) => $city = Arr::add($city, 'updated_at', now()));
        $this->database->table('cities')->insertOrIgnore($cities);
    }

    private function createProviders(array $servers, string $providerName): void
    {
        $this->database->table('vpn_providers')->insertOrIgnore(['name' => $providerName]);
    }

    private function flattenIpAddresses(array $server, string $providerName): array
    {
        return Collection::make($server['ips'])
            ->map(function (string $ip) use ($server, $providerName) {
                return new VpnServerDTO(
                    $ip,
                    filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 6 : 4,
                    VpnProvider::query()->where('name', $providerName)->get()->sole(),
                    Country::query()->where('name', $server['country'])->get()->sole(),
                    $server['hostname'] ?? null,
                    array_key_exists('region', $server)
                        ? Region::query()->where('name', $server['region'])->get()->sole()
                        : null,
                    array_key_exists('city', $server)
                        ? City::query()->where('name', $server['city'])->get()->sole()
                        : null,
                    $server['vpn'] ?? null
                );
            })
            ->toArray();
    }

    private function createVpnServers(array $servers, string $providerName): void
    {
        LazyCollection::make($servers)
            ->map(fn(array $server) => $this->flattenIpAddresses($server, $providerName))
            ->flatten(1)
            ->tap($this->createVpnServersFromDTOs(...));
    }

    private function createVpnServersFromDTOs(LazyCollection $vpnServerDtos): void
    {
        $insertReadyVpnServers = $vpnServerDtos
            ->map(function (VpnServerDTO $serverDto) {
                return [
                    'ip_address' => $serverDto->ipAddress,
                    'ip_version' => $serverDto->ipVersion,
                    'hostname' => $serverDto->hostname,
                    'protocol' => $serverDto->protocol?->value,
                    'vpn_provider_id' => $serverDto->vpnProvider->id,
                    'country_id' => $serverDto->country->id,
                    'region_id' => $serverDto->region?->id,
                    'city_id' => $serverDto->city?->id,
                ];
            });

        $insertReadyVpnServers->chunk(1000)
            ->each(function (LazyCollection $chunk) {
                VpnServer::query()->insert($chunk->toArray());
            });
    }

}
