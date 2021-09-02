<?php

namespace Just\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Just\Models\Panel;

class PanelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Panel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location' => 'content',
            'layout_id' => $this->faker->name,
            'type' => 'float',
            'orderNo' => 1920
        ];
    }
}
