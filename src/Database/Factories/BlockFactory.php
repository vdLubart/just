<?php

namespace Just\Database\Factories;

use Just\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Block::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'text',
            'panelLocation' => 'content',
            'page_id' => 1,
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'width' => 12,
            'layoutClass' => 'primary',
            'cssClass' => '',
            'orderNo' => 1,
            'isActive' => 1,
            'parameters' => json_decode('{}'),
            'parent' => null
        ];
    }

    public function inHeader() {
        return $this->state(function (array $attributes) {
            return [
                'panelLocation'=>'header'
            ];
        });
    }
}
