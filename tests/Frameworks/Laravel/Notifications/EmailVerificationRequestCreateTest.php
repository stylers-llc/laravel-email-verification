<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Notifications;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Stylers\EmailVerification\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\EmailVerificationService;
use Stylers\EmailVerification\Frameworks\Laravel\Fixtures\Models\User;

class EmailVerificationRequestCreateTest extends BaseTestCase
{
    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_notify()
    {
        Notification::fake();

        $expectedName = 'Johnny Doe Test';
        /** @var User $verifiableUser */
        $verifiableUser = factory(User::class)->create([
            'name' => $expectedName
        ]);
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createEmailVerificationRequest($verifiableUser);

        Route::get('email/verify/{token}', function(){})->name(config('email-verification.route'));
        $expectedUrl = route(config('email-verification.route'), ['token' => $verificationRequest->getToken()]);
        $verificationService->sendNotification($verificationRequest);

        Notification::assertSentTo(
            $verifiableUser,
            EmailVerificationRequestCreate::class,
            function (EmailVerificationRequestCreate $notification) use ($verifiableUser, $expectedUrl, $expectedName) {
                $mailData = $notification->toMail($verifiableUser)->toArray();

                $this->assertContains($expectedUrl, $mailData['actionUrl']);

                $expectedArray = $notification->toArray($verifiableUser);
                $this->assertEquals($expectedUrl, $expectedArray['verification_url']);
                $this->assertEquals($expectedName, $expectedArray['recipient_name']);

                return $notification->getVerificationUrl() === $expectedUrl
                    && $notification->getRecipientName() === $expectedName
                    ;
            }
        );
    }
}
