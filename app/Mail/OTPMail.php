<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Mã xác thực đổi mật khẩu - EventHub')
                    ->html("
                        <h1>Mã xác thực của bạn</h1>
                        <p>Mã OTP để đổi mật khẩu của bạn là: <strong style='font-size: 24px; color: #4F46E5'>{$this->otp}</strong></p>
                        <p>Mã này sẽ hết hạn sau 5 phút.</p>
                        <p>Vui lòng không chia sẻ mã này cho ai.</p>
                    ");
    }
}