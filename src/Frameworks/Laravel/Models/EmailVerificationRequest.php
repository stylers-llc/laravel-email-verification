<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\EmailVerificationRequestInterface;

class EmailVerificationRequest extends Model implements EmailVerificationRequestInterface
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'token',
        'verified_at',
    ];

    protected $dates = [
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getExpirationDate(): \DateTimeInterface
    {
        $expirationDate = clone $this->getAttribute('created_at');
        $expirationDate->addMinutes(config('email-verification.expire'));
        return $expirationDate;
    }

    public function getVerificationDate(): ?\DateTimeInterface
    {
        return $this->verified_at;
    }

    public function setVerificationDate(\DateTimeInterface $verifiedAt = null)
    {
        $this->verified_at = $verifiedAt;
    }

    public function getVerifiable(): ?EmailVerifiableInterface
    {
        return $this->morphTo('verifiable')->first();
    }

    public function setVerifiable(EmailVerifiableInterface $emailVerifiable)
    {
        $this->verifiable_type = get_class($emailVerifiable);
        $this->verifiable_id = $emailVerifiable->getId();
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}