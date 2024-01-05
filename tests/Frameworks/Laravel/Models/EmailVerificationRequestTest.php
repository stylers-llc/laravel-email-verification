<?php

namespace Stylers\EmailVerification\Tests\Frameworks\Laravel\Models;

use Carbon\Carbon;
use Stylers\EmailVerification\Tests\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;

class EmailVerificationRequestTest extends BaseTestCase
{
    /** @var EmailVerificationRequest */
    private $verificationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->verificationRequest = factory(EmailVerificationRequest::class)->make([
            'email' => 'test@test.com',
            'token' => 'test-token',
            'created_at' => new Carbon(),
        ]);
    }

    /**
     * @test
     */
    public function it_can_set_email()
    {
        $this->verificationRequest->setEmail('other-email');
        $this->assertEquals('other-email', $this->verificationRequest->getEmail());
    }

    /**
     * @test
     */
    public function it_can_set_verification_date()
    {
        $expectedDate = new \DateTime();
        $this->verificationRequest->setVerificationDate($expectedDate);
        $this->assertEquals($expectedDate, $this->verificationRequest->getVerificationDate());
    }

    /**
     * @test
     */
    public function it_can_set_token()
    {
        $this->verificationRequest->setToken('other-token');
        $this->assertEquals('other-token', $this->verificationRequest->getToken());
    }

    /**
     * @test
     */
    public function it_can_set_type()
    {
        $expectedType = 'user';
        $this->verificationRequest->setType($expectedType);
        $this->assertEquals($expectedType, $this->verificationRequest->getType());
    }

    /**
     * @test
     */
    public function check_expiration_date()
    {
        $this->assertEquals(
            $this->verificationRequest->created_at->addMinutes(config('email-verification.expire')),
            $this->verificationRequest->getExpirationDate()
        );
    }
}
