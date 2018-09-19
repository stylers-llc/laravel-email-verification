<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\EmailVerificationRequestInterface;
use Stylers\EmailVerification\Frameworks\Laravel\Events\VerificationSuccess;
use Stylers\EmailVerification\Tests\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Tests\Frameworks\Laravel\Fixtures\Models\User;
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
        Event::fake();

        $email = 'test@test.com';
        $type = 'user';
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createRequest($email, $type);
        $verificationService->verify($verificationRequest->getToken());

        $this->assertTrue($verificationService->isEmailVerified($email, $type));

        Event::assertDispatched(
            VerificationSuccess::class,
            function(VerificationSuccess $e) use ($verificationRequest) {
                return $e->getVerificationRequest()->id === $verificationRequest->id;
            }
        );
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
        Event::fake();
        $verificationService = new EmailVerificationService();
        try {
            $verificationService->verify('non-existing-token');
        } catch (\InvalidArgumentException $exception) {
            Event::assertNotDispatched(VerificationSuccess::class);
            throw $exception;
        }
    }

    /**
     * @test
     * @expectedException  \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_cannot_verify_expired()
    {
        Event::fake();
        /** @var EmailVerifiableInterface $verifiableUser */
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $expiredDateTime = new Carbon();
        $expiredDateTime->subMinutes(config('email-verification.expire'));
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = factory(EmailVerificationRequest::class)->create([
            'created_at' => $expiredDateTime
        ]);

        try {
            $verificationService->verify($verificationRequest->getToken());
        } catch (\Stylers\EmailVerification\Exceptions\ExpiredVerificationException $exception) {
            Event::assertNotDispatched(VerificationSuccess::class);
            throw $exception;
        }
    }

    /**
     * @test
     * @expectedException  \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Stylers\EmailVerification\Exceptions\ExpiredVerificationException
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_cannot_verify_already_verified()
    {
        Event::fake();
        /** @var EmailVerifiableInterface $verifiableUser */
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = factory(EmailVerificationRequest::class)->create([
            'verified_at' => new Carbon()
        ]);

        try {
            $verificationService->verify($verificationRequest->getToken());
        } catch (\Stylers\EmailVerification\Exceptions\AlreadyVerifiedException $exception) {
            Event::assertNotDispatched(VerificationSuccess::class);
            throw $exception;
        }
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     */
    public function it_can_create_email_verification_request()
    {
        $verifiableUser = factory(User::class)->create();
        $verificationService = new EmailVerificationService();
        $verificationRequest = $verificationService->createRequest($verifiableUser);
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
        $verificationRequest = $verificationService->createRequest($verifiableUser);
        $verificationService->verify($verificationRequest->getToken());
        $verificationRequest = $verificationService->createRequest($verifiableUser);

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
        $verificationRequestFirst = $verificationService->createRequest($verifiableUser->email);
        $verificationRequestSecond = $verificationService->createRequest($verifiableUser->email);
        $verificationRequestFirst->refresh();

        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequestFirst);
        $this->assertInstanceOf(EmailVerificationRequestInterface::class, $verificationRequestSecond);
        $this->assertNotEquals($verificationRequestFirst->id, $verificationRequestSecond->id);
    }

    /**
     * @test
     * @throws \Stylers\EmailVerification\Exceptions\AlreadyVerifiedException
     * @throws \Exception
     */
    public function it_can_invalidate_request()
    {
        $service = new EmailVerificationService();
        $email = 'test@test.com';
        $type = 'user';
        $request = $service->createRequest($email, $type);
        $service->revokeRequest($email, $type);
        $request->refresh();
        $this->assertNotNull($request->deleted_at);
    }
}
