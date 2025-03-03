<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $email;
    public $hash;

    public function __construct($email, $hash)
    {
        $this->email = $email;
        $this->hash  = $hash;
    }

    public function build()
    {
        return $this->view('emails.convite')
            ->with(['email' => $this->email,
                     'hash' => $this->hash]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite Mail',
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
