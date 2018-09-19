<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

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
        if ($this->isEmailVerified($email, $type)) {
            throw new AlreadyVerifiedException();
        }
        $this->revokeRequest($email, $type);

        return $this->requestDAO->create([
            'email' => $email,
            'type' => $type,
            'token' => uniqid() . str_random(),
        ]);
    }

    /**
     * @param string $email
     * @param string|null $type
     * @throws \Exception
     */
    public function revokeRequest(string $email, string $type = null)
    {
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->requestDAO->where('email', $email)->where('type', $type)->first();
        if ($verificationRequest) {
            $verificationRequest->delete();
        }
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
     * @throws \Throwable
     */
    public function verify(string $token): EmailVerificationRequestInterface
    {
        /** @var EmailVerificationRequest $requestInstance */
        $requestInstance = $this->requestDAO->where('token', $token)->first();
        $now = new \DateTime();
        $this->validateVerificationRequest($requestInstance, $now);

        \DB::transaction(function () use ($requestInstance, $now) {
            $requestInstance->setVerificationDate($now);
            $requestInstance->save();
            $this->removeOtherVerificationRequests($requestInstance);
            event(new VerificationSuccess($requestInstance));
        });

        return $requestInstance;
    }

    public function isEmailVerified(string $email, string $type = null): bool
    {
        return (bool)$this->requestDAO
            ->where('email', $email)
            ->where('type', $type)
            ->whereNotNull('verified_at')
            ->count();
    }

    /**
     * @param EmailVerificationRequestInterface $requestInstance
     * @param \DateTimeInterface|null $expirationDate
     * @throws AlreadyVerifiedException
     * @throws ExpiredVerificationException
     */
    private function validateVerificationRequest(
        ?EmailVerificationRequestInterface $requestInstance,
        \DateTimeInterface $expirationDate
    ): void
    {
        if ($requestInstance) {
            if ($requestInstance->getExpirationDate() <= $expirationDate) {
                throw new ExpiredVerificationException();
            }
            if ($requestInstance->isVerified()) {
                throw new AlreadyVerifiedException();
            }
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @param $requestInstance
     */
    private function removeOtherVerificationRequests($requestInstance): void
    {
        $othersVerificationRequests = $this->requestDAO
            ->where('id', '!=', $requestInstance->id)
            ->where('email', $requestInstance->getEmail())
            ->whereNull('verified_at')
            ->get();

        $othersVerificationRequests->each(function (EmailVerificationRequest $verificationRequest) {
            $verificationRequest->delete();
        });
    }
}
