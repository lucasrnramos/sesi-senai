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
    public $tipo_envio;

    public function __construct($email, $hash, $tipo_envio)
    {
        $this->email      = $email;
        $this->hash       = $hash;
        $this->tipo_envio = $tipo_envio;
    }

    public function build()
    {
        if ($this->tipo_envio == 1) {
            return $this->view('emails.convite')
                ->with(['url'   => 'cadastrar/',
                        'email' => $this->email,
                        'hash'  => $this->hash]);

        } else if ($this->tipo_envio == 2) {
            return $this->view('emails.redefinir-senha')
                ->with(['url'   => 'recuperar-senha/',
                        'email' => $this->email,
                        'hash'  => $this->hash]);
        }

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
