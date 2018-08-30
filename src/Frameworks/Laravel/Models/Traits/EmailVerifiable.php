<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;

trait EmailVerifiable
{
    use Notifiable;

    public function getVerifiableEmail(): string
    {
        return $this->email;
    }

    public function getVerifiableName(): string
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function emailVerificationRequests(): MorphMany
    {
        return $this->morphMany(EmailVerificationRequest::class, 'verifiable');
    }

    public function isEmailVerified(): bool
    {
        return (bool)$this->emailVerificationRequests()
            ->where('email', $this->getVerifiableEmail())
            ->whereNotNull('verified_at')
            ->first();
    }
    
    public function routeNotificationForMail()
    {
        return $this->getVerifiableEmail();
    }
}
