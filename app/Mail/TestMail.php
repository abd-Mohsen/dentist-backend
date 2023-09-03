<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    private $otp;

    
    public function __construct($otp)
    {
        $this->$otp = $otp;
    }

    public function build(){
        return $this->view('mail', ['otp' => $this->$otp]);
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Mail',
        );
    }

   
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
