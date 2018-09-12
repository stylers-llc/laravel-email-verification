[![Build Status](https://travis-ci.org/stylers-llc/laravel-email-verification.svg?branch=master)](https://travis-ci.org/stylers-llc/laravel-email-verification)

# Laravel Email Verification (non-released)

## TODO
- [ ] Release
- [ ] Publish on [Packagist](https://packagist.org/)

## Requirements
- PHP >= 7.1.3
- Laravel ~5.x

## Installation
```bash
composer require stylers/laravel-email-verification
```

## Publish the assets
```bash
php artisan vendor:publish --tag=laravel-mail
php artisan vendor:publish --provider="Themsaid\MailPreview\MailPreviewServiceProvider"
php artisan vendor:publish --provider="Stylers\EmailVerification\Frameworks\Laravel\ServiceProvider"
```

## Run the migrations
```bash
php artisan migrate
```

## Usage

### Set up the abstraction
```php
use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\Frameworks\Laravel\Models\Traits\EmailVerifiable;

class User extends Model implements EmailVerifiableInterface
{
    use EmailVerifiable;
}
```

### Register your listeners to events
```php
// app/Providers/EventServiceProvider.php

    protected $listen = [
        ...
        'Stylers\EmailVerification\Frameworks\Laravel\Events\VerificationSuccess' => [
            // your listeners
        ]
    ];
```

### Example of generating email-verification-request

Make your own route to create verification-request. Write the code below into the routes/web.php and implement your action
```php
Route::post('/email-verification-request', 'AnyController@createEmailVerificationRequest')
    ->name('email-verification-request.create');
```

```php
use Stylers\EmailVerification\Frameworks\Laravel\Notifications\EmailVerificationRequestCreate;
use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
...
class AnyController extends Controller {
    ...
    public function createRequest(
        Request $request, 
        EmailVerificationRequestInterface $emailVerificationService
    )
    {
        $verifiableUser = User::first();
        try {
            $verificationRequest = $emailVerificationService->createRequest($verifiableUser);
            $emailVerificationService->sendNotification($verificationRequest);
        } catch (AlreadyVerifiedException $e) {
            // handle exception
        }

        ...
    }
}


```

### Example of verification

Make your own verification route. Write the code below into the routes/web.php and implement your action
```php
Route::get('/email/verify/{token}', 'AnyController@verifyEmail')
    ->name(config('email-verification.route'));
```

Implement your verifyEmail method in AnyController
```php
...
use Stylers\EmailVerification\Exceptions\ExpiredVerificationException;
use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
use Stylers\EmailVerification\EmailVerificationServiceInterface;
...
class AnyController extends Controller {
    ...
    public function verifyEmail(
        Request $request, 
        EmailVerificationServiceInterface $emailVerificationService
    )
    {
        $token = $request->input('token');
        try {
            $emailVerificationRequest = $verificationService->verify($token);
        } catch(ExpiredVerificationException $e) {
            // expired verification token
        } catch(AlreadyVerifiedException $e) {
            // already verified email
        } catch(\InvalidArgumentException $e) {
            // non-existing token
        }
        ...
    }
}
```
