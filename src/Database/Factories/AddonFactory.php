<?php

namespace Just\Database\Factories;

use Just\Models\AddOn;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AddOn::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'block_id' => 1,
            'type' => 'phrase',
            'name' => 'addon',
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'orderNo' => 1,
            'isActive' => 1,
            'parameters' => '{}'
        ];
    }

    public function indentify(int $blockId, string $type = 'phrase') {
        return $this->state(function (array $attributes) use ($blockId, $type){
            return [
                'block_id' => $blockId,
                'type' => $type,
            ];
        });
    }

    public function type($type) {
        return $this->state(function (array $attributes) use ($type){
            return [
                'type' => $type
            ];
        });
    }

    public function name($name) {
        return $this->state(function (array $attributes) use ($name){
            return [
                'name' => $name
            ];
        });
    }
}
