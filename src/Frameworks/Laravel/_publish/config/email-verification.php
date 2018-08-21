<?php

return [
    // in minutes
    'expire' => 60,

    // make your route with token parameter
    // e.g.: Route::get('/email/verify/{token}', 'AnyController@emailVerify')->name(config('email-verification.route'));
    'route' => 'email.verification',
];