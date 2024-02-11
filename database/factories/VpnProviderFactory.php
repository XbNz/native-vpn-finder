<?php

namespace Database\Factories;

use App\Models\VpnProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VpnProviderFactory extends Factory
{
    protected $model = VpnProvider::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
