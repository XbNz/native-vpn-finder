<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city(),
            'country_id' => Country::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
