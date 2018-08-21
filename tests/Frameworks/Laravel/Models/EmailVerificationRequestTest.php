<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models;

use Carbon\Carbon;
use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\Fixtures\Models\User;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;

class EmailVerificationRequestTest extends BaseTestCase
{
    /** @var EmailVerificationRequest */
    private $verificationRequest;

    /** @var EmailVerifiableInterface */
    private $verifiable;

    protected function setUp()
    {
        parent::setUp();
        $this->verifiable = factory(User::class)->create();
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
    public function it_can_set_verifiable()
    {
        $this->verificationRequest->setVerifiable($this->verifiable);
        $this->assertEquals($this->verifiable->getVerifiableEmail(), $this->verificationRequest->getVerifiable()->getVerifiableEmail());
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
