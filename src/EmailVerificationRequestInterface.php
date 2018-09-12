<?php


namespace Stylers\EmailVerification;


interface EmailVerificationRequestInterface
{
    public function getEmail(): string;

    public function setEmail(string $email);

    public function getToken(): string;

    public function setToken(string $token);

    public function getExpirationDate(): \DateTimeInterface;

    public function getVerificationDate(): ?\DateTimeInterface;

    public function setVerificationDate(\DateTimeInterface $verifiedAt = null);

    public function getType(): ?string;

    public function setType(string $type = null);

    public function isVerified(): bool;
}