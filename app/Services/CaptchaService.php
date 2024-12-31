<?php

namespace App\Services;

use Mobicms\Captcha\Code;
use Mobicms\Captcha\Image;

class CaptchaService
{
    public function generateCode(): string
    {
        $code = new Code(
            config('captcha.length_min'),
            config('captcha.length_max'),
            config('captcha.character_set')
        );
        return (string) $code;
    }

    public function generateImage(string $code): string
    {
        return (string) new Image($code);
    }
}
