<?php

namespace Stylers\EmailVerification;

interface EmailVerifiableInterface
{
    public function isEmailVerified(): bool;

    public function setEmailVerified(bool $emailVerified);
}
