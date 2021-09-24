<?php

namespace Just\Database\Factories;

use Just\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Just\Models\System\Route;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $route = Route::factory()->create();
        return [
            'title' => $this->faker->name,
            'description' => $this->faker->sentence,
            'keywords' => '',
            'author' => $this->faker->name,
            'copyright' => '',
            'route' => $route->route,
            'layout_id' => 1,
            'isActive' => 1
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
