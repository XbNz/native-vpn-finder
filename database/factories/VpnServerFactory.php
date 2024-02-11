<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\VpnProvider;
use App\Models\VpnServer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VpnServerFactory extends Factory
{
    protected $model = VpnServer::class;

    public function definition()
    {
        return [
            'ip_address' => $this->faker->ipv4(),
            'domain' => $this->faker->domainName(),
            'vpn_provider_id' => VpnProvider::factory(),
            'country_id' => Country::factory(),
            'city_id' => City::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
