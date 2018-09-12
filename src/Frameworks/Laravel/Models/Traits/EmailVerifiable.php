<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models\Traits;

trait EmailVerifiable
{
    public function isEmailVerified(): bool
    {
        return (bool)$this->email_verified;
    }

    public function setEmailVerified(bool $emailVerified)
    {
        $this->email_verified = $emailVerified;
    }
}
