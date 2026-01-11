<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;
    public $expiryMinutes = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp, $name)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Email Verification Code - eLibrary')
                    ->view('emails.otp_verification')
                    ->with([
                        'otp' => $this->otp,
                        'name' => $this->name,
                        'expiryMinutes' => $this->expiryMinutes,
                    ]);
    }
}
