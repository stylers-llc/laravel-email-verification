<?php

namespace Stylers\EmailVerification;

use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
use Stylers\EmailVerification\Exceptions\ExpiredVerificationException;
use Stylers\EmailVerification\Frameworks\Laravel\Contracts\EmailVerifiableInterface;

interface EmailVerificationServiceInterface
{
    /**
     * @param EmailVerifiableInterface $emailVerifiable
     * @return EmailVerificationRequestInterface
     * @throws AlreadyVerifiedException
     * @throws \Exception
     */
    public function createEmailVerificationRequest(EmailVerifiableInterface $emailVerifiable): EmailVerificationRequestInterface;

    /**
     * @param EmailVerificationRequestInterface $verificationRequest
     */
    public function sendNotification(EmailVerificationRequestInterface $verificationRequest): void;

    /**
     * @param string $token
     * @return EmailVerificationRequestInterface
     * @throws ExpiredVerificationException
     * @throws AlreadyVerifiedException
     * @throws \InvalidArgumentException
     */
    public function verify(string $token): EmailVerificationRequestInterface;
}