<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $verificationUrl;

    public function __construct($firstName, $verificationUrl)
    {
        $this->firstName = $firstName;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('Verify Your Samuel Clinic Account')
            ->view('emails.account_verification');
    }
}
