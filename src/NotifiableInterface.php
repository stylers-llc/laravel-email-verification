<?php


namespace Stylers\EmailVerification;


interface NotifiableInterface
{
    public function notify($notificationInstance);

    public function getName(): string;
}