<?php

namespace Stylers\EmailVerification\Frameworks\Laravel\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationRequestCreate extends Notification
{
    use Queueable;
    /**
     * @var string
     */
    private $verificationUrl;
    /**
     * @var string
     */
    private $recipientName;

    /**
     * Create a new notification instance.
     *
     * @param string $verificationUrl
     * @param string $recipientName
     */
    public function __construct(string $verificationUrl, string $recipientName = '')
    {
        $this->verificationUrl = $verificationUrl;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Email verification')
                    ->greeting('Dear ' . $this->recipientName)
                    ->line('Please verify your e-mail')
                    ->action('Verify', $this->verificationUrl)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'verification_url' => $this->verificationUrl,
            'recipient_name' => $this->recipientName,
        ];
    }

    /**
     * @return string
     */
    public function getVerificationUrl(): string
    {
        return $this->verificationUrl;
    }

    /**
     * @return string
     */
    public function getRecipientName(): string
    {
        return $this->recipientName;
    }
}
