<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models\Traits;

trait EmailVerifiable
{
    public function getVerificationType(): ?string
    {
        return null;
    }
}
