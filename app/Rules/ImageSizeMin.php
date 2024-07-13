<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Imagick;

class ImageSizeMin implements ValidationRule
{
    public function __construct(
        public $width = 400,
        public $height = 400,
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     * @throws \ImagickException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $imagick = new Imagick();
        [, $data] = explode(';base64', $value);
        $imagick->readImageBlob(base64_decode($data));

        if ($imagick->getImageWidth() < $this->width) {
            $fail('The minimum width of image is ' . $this->width . ' pixels');
        }

        if ($imagick->getImageHeight() < $this->height) {
            $fail('The minimum height of image is ' . $this->height . ' pixels');
        }
    }
}
