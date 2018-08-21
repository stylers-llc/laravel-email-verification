<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Stylers\EmailVerification\Frameworks\Laravel\Contracts\EmailVerifiableInterface;
use Stylers\EmailVerification\EmailVerificationRequestInterface;
use Stylers\EmailVerification\EmailVerificationServiceInterface;
use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
use Stylers\EmailVerification\Exceptions\ExpiredVerificationException;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;
use Stylers\EmailVerification\Frameworks\Laravel\Notifications\EmailVerificationRequestCreate;

class EmailVerificationService implements EmailVerificationServiceInterface
{
    /**
     * @param EmailVerifiableInterface $emailVerifiable
     * @return EmailVerificationRequestInterface
     * @throws AlreadyVerifiedException
     * @throws \Exception
     */
    public function createEmailVerificationRequest(EmailVerifiableInterface $emailVerifiable): EmailVerificationRequestInterface
    {
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = $emailVerifiable
            ->emailVerificationRequests()
            ->where('email', $emailVerifiable->getVerifiableEmail())
            ->first();

        if ($verificationRequest) {
            if ($verificationRequest->isVerified()) {
                throw new AlreadyVerifiedException();
            }
            $verificationRequest->delete();
        }

        $verificationRequest = new EmailVerificationRequest([
            'email' => $emailVerifiable->getVerifiableEmail(),
            'token' => uniqid() . str_random(),
        ]);
        $verificationRequest->setVerifiable($emailVerifiable);
        $verificationRequest->save();
        return $verificationRequest;
    }

    /**
     * @param EmailVerificationRequestInterface $verificationRequest
     */
    public function sendNotification(EmailVerificationRequestInterface $verificationRequest)
    {
        $verificationUrl = route(config('email-verification.route'), ['token' => $verificationRequest->getToken()]);

        $emailVerifiable = $verificationRequest->getVerifiable();
        $notification = new EmailVerificationRequestCreate($verificationUrl, $emailVerifiable->getVerifiableName());

        // TODO: add notify method to EmailVerifiableInterface
        $emailVerifiable->notify($notification);
    }

    /**
     * @param string $token
     * @throws ExpiredVerificationException
     * @throws AlreadyVerifiedException
     * @throws \InvalidArgumentException
     */
    public function verify(string $token): void
    {
        /** @var EmailVerificationRequest $verificationRequestDAO */
        $verificationRequestDAO = new EmailVerificationRequest();

        /** @var EmailVerificationRequest $verificationRequestInstance */
        $verificationRequestInstance = $verificationRequestDAO->where('token', $token)->first();
        if ($verificationRequestInstance) {
            $now = new \DateTime();
            if ($verificationRequestInstance->getExpirationDate() <= $now) {
                throw new ExpiredVerificationException();
            }
            if ($verificationRequestInstance->isVerified()) {
                throw new AlreadyVerifiedException();
            }
            $verificationRequestInstance->setVerificationDate($now);
            $verificationRequestInstance->save();
        } else {
            throw new \InvalidArgumentException();
        }
    }
}