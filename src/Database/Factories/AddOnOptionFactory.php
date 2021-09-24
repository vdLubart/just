<?php

namespace Just\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Just\Models\Blocks\AddOns\AddOnOption;

class AddOnOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AddOnOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'add_on_id' => 1,
            'option' => $this->faker->word,
            'isActive' => 1,
            'orderNo' => 1
        ];
    }

    public function deactivate() {
        return $this->state(function (array $attributes){
            return [
                'isActive' => 0
            ];
        });
    }
}
