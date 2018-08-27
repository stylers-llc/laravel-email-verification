<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Events;

use Illuminate\Queue\SerializesModels;
use Stylers\EmailVerification\EmailVerificationRequestInterface;

class VerificationSuccess
{
    use SerializesModels;

    /**
     * @var EmailVerificationRequestInterface
     */
    private $verificationRequest;

    public function __construct(EmailVerificationRequestInterface $verificationRequest)
    {
        $this->verificationRequest = $verificationRequest;
    }

    /**
     * @return EmailVerificationRequestInterface
     */
    public function getVerificationRequest(): EmailVerificationRequestInterface
    {
        return $this->verificationRequest;
    }
}
