<?php

namespace Stylers\EmailVerification;

interface EmailVerifiableInterface
{
    public function getVerificationType(): ?string;
}
