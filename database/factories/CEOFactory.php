<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'company_name' => $faker->unique()->company,
        'year' => $faker->year,
        'company_headquarters' => $faker->city,
        'what_company_does' => $faker->sentence
    ];
});
