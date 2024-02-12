<?php

namespace Database\Factories;

use App\Enums\Protocol;
use App\Models\City;
use App\Models\Country;
use App\Models\VpnProvider;
use App\Models\VpnServer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class VpnServerFactory extends Factory
{
    protected $model = VpnServer::class;

    public function definition()
    {
        return [
            'vpn_provider_id' => VpnProvider::factory(),
            'country_id' => Country::factory(),
            'city_id' => City::factory(),
            'protocol' => $this->faker->randomElement(Arr::pluck(Protocol::cases(), 'value')),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
