<?php

namespace Database\Factories;

use App\Models\ServerNetworkDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServerNetworkDetailFactory extends Factory
{
    protected $model = ServerNetworkDetail::class;

    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->ipv4(),
            'ip_version' => 4,
            'hostname' => $this->faker->domainName(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
