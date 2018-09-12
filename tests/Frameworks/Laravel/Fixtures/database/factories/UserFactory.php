<?php

use Faker\Generator as Faker;

$factory->define('Stylers\EmailVerification\Tests\Frameworks\Laravel\Fixtures\Models\User', function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});