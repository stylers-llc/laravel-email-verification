<?php

namespace Stylers\EmailVerification;

use Stylers\EmailVerification\Frameworks\Laravel\Contracts\EmailVerifiableInterface;

interface EmailVerificationServiceInterface
{
    /**
     * @param EmailVerifiableInterface $emailVerifiable
     * @return EmailVerificationRequestInterface
     */
    public function createEmailVerificationRequest(EmailVerifiableInterface $emailVerifiable): EmailVerificationRequestInterface;

    /**
     * @param string $token
     */
    public function verify(string $token): void;
}