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

$factory->define(Just\Structure\Panel::class, function (Faker $faker){
    return [
        'location' => 'content',
        'layout_id' => $faker->name,
        'type' => 'float',
        'orderNo' => 1920
    ];
});
