<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetOtpMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;

    public function __construct($otp,$name)
    {
        $this->otp = $otp;
        $this->name=$name;
    }

    public function build()
    {
        return $this->view('resetOtpEmail')
                    ->with(['otp' => $this->otp,'name'=>$this->name]);
    }
}