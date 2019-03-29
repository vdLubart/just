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

$factory->define(\Lubart\Just\Structure\Panel\Block::class, function (Faker $faker){
    return [
        'name' => 'text',
        'panelLocation' => 'header',
        'page_id' => null,
        'title' => $faker->name,
        'description' => $faker->sentence,
        'width' => 12,
        'layoutClass' => 'primary',
        'cssClass' => '',
        'orderNo' => 1,
        'isActive' => 1,
        'parameters' => '{}',
        'parent' => null
    ];
});
