<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly UserSubscription $userSubscription
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Subscription Confirmed — VidyaMission');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_confirmation',
            with: [
                'user'             => $this->user,
                'subscription'     => $this->userSubscription->subscription,
                'userSubscription' => $this->userSubscription,
            ]
        );
    }
}
