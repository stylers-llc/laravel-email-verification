<?php

use Faker\Generator as Faker;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;


$factory->define(EmailVerificationRequest::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'token' => 'test-token',
    ];
});