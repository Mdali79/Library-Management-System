<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;
    public $expiryMinutes = 15;

    public function __construct($otp, $name)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Password Reset Code - eLibrary')
            ->view('emails.forgot_password_otp')
            ->with([
                'otp' => $this->otp,
                'name' => $this->name,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}
