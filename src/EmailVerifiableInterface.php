<?php

namespace Stylers\EmailVerification;

interface EmailVerifiableInterface
{
    public function getVerifiableEmail(): string;

    public function getVerifiableName(): string;

    public function getId();

    public function isEmailVerified(): bool;

    public function emailVerificationRequests();

    public function notify($notificationInstance);
}