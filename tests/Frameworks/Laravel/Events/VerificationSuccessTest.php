<?php

namespace Stylers\EmailVerification\Tests\Frameworks\Laravel\Events;

use Stylers\EmailVerification\Tests\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\Events\VerificationSuccess;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;

class VerificationSuccessTest extends BaseTestCase
{
    /** @test */
    public function it_can_get_email_verification_request()
    {
        $expectedVerificationRequest = new EmailVerificationRequest();
        $event = new VerificationSuccess($expectedVerificationRequest);
        $this->assertEquals($expectedVerificationRequest, $event->getVerificationRequest());
    }
}
