<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Illuminate\Support\Carbon;
use Stylers\EmailVerification\Frameworks\Laravel\Contracts\EmailVerifiableInterface;
use Stylers\EmailVerification\EmailVerificationRequestInterface;
use Stylers\EmailVerification\Frameworks\Laravel\Fixtures\Models\User;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;

class EmailVerificationServiceTest extends BaseTestCase
{
    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     */
    public function it_can_verify()
    {
        /** @var EmailVerifiableInterface $verifiableUser */
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createEmailVerificationRequest($verifiableUser);
        $verificationService->verify($verificationRequest->getToken());

        $this->assertTrue($verifiableUser->isVerified());
    }

    /**
     * @test
     * @expectedException  \InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_cannot_verify_non_existing()
    {
        $verificationService = new EmailVerificationService();
        $verificationService->verify('non-existing-token');
    }

    /**
     * @test
     * @expectedException  \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_cannot_verify_expired()
    {
        /** @var EmailVerifiableInterface $verifiableUser */
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $expiredDateTime = new Carbon();
        $expiredDateTime->subMinutes(config('email-verification.expire'));
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = factory(EmailVerificationRequest::class)->make([
            'created_at' => $expiredDateTime
        ]);
        $verificationRequest->setVerifiable($verifiableUser);
        $verificationRequest->save();

        $verificationService->verify($verificationRequest->getToken());
    }

    /**
     * @test
     * @expectedException  \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_cannot_verify_already_verified()
    {
        /** @var EmailVerifiableInterface $verifiableUser */
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = factory(EmailVerificationRequest::class)->make([
            'verified_at' => new Carbon()
        ]);
        $verificationRequest->setVerifiable($verifiableUser);
        $verificationRequest->save();

        $verificationService->verify($verificationRequest->getToken());
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_create_email_verification_request()
    {
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createEmailVerificationRequest($verifiableUser);
        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequest);
    }

    /**
     * @test
     * @expectedException \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     */
    public function it_cannot_create_email_verification_request_that_already_verified()
    {
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createEmailVerificationRequest($verifiableUser);
        $verificationService->verify($verificationRequest->getToken());
        $verificationRequest = $verificationService->createEmailVerificationRequest($verifiableUser);

        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequest);
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_create_email_verification_request_only_previous_is_non_verified()
    {
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $verificationRequestFirst = $verificationService->createEmailVerificationRequest($verifiableUser);
        $verificationRequestSecond = $verificationService->createEmailVerificationRequest($verifiableUser);
        $verificationRequestFirst->refresh();

        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequestFirst);
        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequestSecond);
        $this->assertNotEquals($verificationRequestFirst->id, $verificationRequestSecond->id);
        $this->assertNotNull($verificationRequestFirst->deleted_at);
    }
}
