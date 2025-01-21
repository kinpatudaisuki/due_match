<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FriendRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $senderName;

    /**
     * Create a new message instance.
     */
    public function __construct($senderName)
    {
        $this->senderName = $senderName;
    }

    public function build()
    {
        return $this->subject('新しいフレンド申請')
                    ->view('emails.friend_request')
                    ->with(['senderName' => $this->senderName]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Friend Request Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.friend_request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
