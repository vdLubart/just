<?php

namespace Just\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Just\Models\System\Route;

class RouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Route::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'route' => $this->faker->word . '/' . $this->faker->word,
            'type' => 'page'
        ];
    }
}
