<?php

namespace Just\Database\Factories;

use Just\Models\Layout;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Layout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'class' => $this->faker->name,
            'width' => 1920,
        ];
    }
}
