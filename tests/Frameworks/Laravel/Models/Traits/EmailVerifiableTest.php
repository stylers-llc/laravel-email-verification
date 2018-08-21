<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models\Traits;

use Stylers\EmailVerification\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\EmailVerificationService;
use Stylers\EmailVerification\Frameworks\Laravel\Fixtures\Models\User;

class EmailVerifiableTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_can_get_verifiable_email()
    {
        $expectedEmail = 'test@test.com';
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create(['email' => $expectedEmail]);
        $this->assertEquals($expectedEmail, $notifiableUser->getVerifiableEmail());
    }

    /**
     * @test
     */
    public function it_can_get_verifiable_name()
    {
        $expectedName = 'John Doe';
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create(['name' => $expectedName]);
        $this->assertEquals($expectedName, $notifiableUser->getVerifiableName());
    }

    /**
     * @test
     */
    public function it_can_get_id()
    {
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create();
        $this->assertEquals($notifiableUser->id, $notifiableUser->getId());
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_get_email_verification_requests()
    {
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create();
        $emailVerificationService = new EmailVerificationService();
        $expectedRequest = $emailVerificationService->createEmailVerificationRequest($notifiableUser);
        $requestOfUser = $notifiableUser->emailVerificationRequests()->first();
        $this->assertEquals($expectedRequest->getEmail(), $requestOfUser->email);
        $this->assertEquals($expectedRequest->getToken(), $requestOfUser->token);
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_check_that_is_verified_false()
    {
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create();
        $emailVerificationService = new EmailVerificationService();
        $emailVerificationService->createEmailVerificationRequest($notifiableUser);
        $this->assertFalse($notifiableUser->isVerified());
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     */
    public function it_can_check_that_is_verified_true()
    {
        /** @var User $notifiableUser */
        $notifiableUser = factory(User::class)->create();
        $emailVerificationService = new EmailVerificationService();
        $request = $emailVerificationService->createEmailVerificationRequest($notifiableUser);
        $emailVerificationService->verify($request->getToken());
        $this->assertTrue($notifiableUser->isVerified());
    }
}
