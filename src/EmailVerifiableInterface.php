<?php

namespace Stylers\EmailVerification;

interface EmailVerifiableInterface
{
    public function getVerifiableEmail(): string;

    public function getVerifiableName(): string;

    public function getId();

    public function isVerified(): bool;

    public function emailVerificationRequests();
}