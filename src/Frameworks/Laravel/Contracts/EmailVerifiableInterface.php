<?php


namespace Stylers\EmailVerification\Frameworks\Laravel\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Stylers\EmailVerification\EmailVerifiableInterface as BaseEmailVerifiableInterface;

interface EmailVerifiableInterface extends BaseEmailVerifiableInterface
{
    /**
     * @return MorphMany
     */
    public function emailVerificationRequests(): MorphMany;
}