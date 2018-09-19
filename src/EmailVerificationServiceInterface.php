<?php

namespace Stylers\EmailVerification;

use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
use Stylers\EmailVerification\Exceptions\ExpiredVerificationException;

interface EmailVerificationServiceInterface
{
    /**
     * @param string $email
     * @param string|null $type
     * @return EmailVerificationRequestInterface
     */
    public function createRequest(string $email, string $type = null): EmailVerificationRequestInterface;

    /**
     * @param string $email
     * @param string|null $type
     */
    public function revokeRequest(string $email, string $type = null);

    /**
     * @param string $token
     * @param NotifiableInterface $notifiable
     */
    public function sendEmail(string $token, NotifiableInterface $notifiable): void;

    /**
     * @param string $token
     * @return EmailVerificationRequestInterface
     * @throws ExpiredVerificationException
     * @throws AlreadyVerifiedException
     * @throws \InvalidArgumentException
     */
    public function verify(string $token): EmailVerificationRequestInterface;

    /**
     * @param string $email
     * @param string $type
     * @return bool
     */
    public function isEmailVerified(string $email, string $type = null): bool;
}