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

    public function __construct(public $otp)
    {
        //$this->$otp = $otp=00000;
    }

    public function build(){
        return $this->subject('otp code')
        //->view('email.mail-template')
        ->text('content');
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'otp code',
        );
    }

   
    // public function content(): Content
    // {
    //     return new Content(
    //         view: null,
    //         text: 'content'
    //     );
    // }

    public function attachments(): array
    {
        return [];
    }
}
