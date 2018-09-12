<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\EmailVerificationRequestInterface;
use Stylers\EmailVerification\EmailVerificationServiceInterface;
use Stylers\EmailVerification\Exceptions\AlreadyVerifiedException;
use Stylers\EmailVerification\Exceptions\ExpiredVerificationException;
use Stylers\EmailVerification\Frameworks\Laravel\Events\VerificationSuccess;
use Stylers\EmailVerification\Frameworks\Laravel\Models\EmailVerificationRequest;
use Stylers\EmailVerification\Frameworks\Laravel\Notifications\EmailVerificationRequestCreate;
use Stylers\EmailVerification\NotifiableInterface;

class EmailVerificationService implements EmailVerificationServiceInterface
{
    /** @var EmailVerificationRequest  */
    private $requestDAO;

    public function __construct()
    {
        $this->requestDAO = app(EmailVerificationRequest::class);
    }

    /**
     * @param string $email
     * @param string|null $type
     * @return EmailVerificationRequestInterface
     * @throws AlreadyVerifiedException
     * @throws \Exception
     */
    public function createRequest(string $email, string $type = null): EmailVerificationRequestInterface
    {
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->requestDAO->where('email', $email)->where('type', $type)->first();

        if ($verificationRequest) {
            if ($verificationRequest->isVerified()) {
                throw new AlreadyVerifiedException();
            }
            return $verificationRequest;
        }

        return $this->requestDAO->create([
            'email' => $email,
            'type' => $type,
            'token' => uniqid() . str_random(),
        ]);
    }

    /**
     * @param string $token
     * @param NotifiableInterface $notifiable
     */
    public function sendEmail(string $token, NotifiableInterface $notifiable): void
    {
        $verificationUrl = route(config('email-verification.route'), ['token' => $token]);
        $notification = new EmailVerificationRequestCreate($verificationUrl, $notifiable->getName());
        $notifiable->notify($notification);
    }

    /**
     * @param string $token
     * @return EmailVerificationRequestInterface
     * @throws AlreadyVerifiedException
     * @throws ExpiredVerificationException
     * @throws \InvalidArgumentException
     */
    public function verify(string $token): EmailVerificationRequestInterface
    {
        /** @var EmailVerificationRequest $requestInstance */
        $requestInstance = $this->requestDAO->where('token', $token)->first();
        if ($requestInstance) {
            $now = new \DateTime();
            if ($requestInstance->getExpirationDate() <= $now) {
                throw new ExpiredVerificationException();
            }
            if ($requestInstance->isVerified()) {
                throw new AlreadyVerifiedException();
            }
            $requestInstance->setVerificationDate($now);
            $requestInstance->save();
        } else {
            throw new \InvalidArgumentException();
        }

        event(new VerificationSuccess($requestInstance));
        
        $othersVerificationRequests = $this->requestDAO
            ->where('id', '!=', $requestInstance->id)
            ->where('email', $requestInstance->getEmail())
            ->whereNull('verified_at')
            ->get();

        $othersVerificationRequests->each(function (EmailVerificationRequest $verificationRequest) {
            $verificationRequest->delete();
        });

        return $requestInstance;
    }

    public function isEmailVerified(string $email, string $type): bool
    {
        return (bool)$this->requestDAO
            ->where('email', $email)
            ->where('type', $type)
            ->whereNotNull('verified_at')
            ->count();
    }
}
