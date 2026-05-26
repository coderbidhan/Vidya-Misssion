<?php

namespace App\Mail;

use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LiveClassReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly LiveClass $liveClass
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Live Class Starting Soon — ' . $this->liveClass->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.live_class_reminder',
            with: [
                'user'      => $this->user,
                'liveClass' => $this->liveClass,
            ]
        );
    }
}
