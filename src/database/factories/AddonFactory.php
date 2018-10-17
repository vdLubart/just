<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Structure\Panel\Block\Addon::class, function (Faker $faker){
    return [
        'block_id' => 1,
        'name' => 'strings',
        'title' => $faker->name,
        'description' => $faker->sentence,
        'orderNo' => 1,
        'isActive' => 1,
        'parameters' => '{}'
    ];
});
