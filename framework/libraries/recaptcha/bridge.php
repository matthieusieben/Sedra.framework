<?php

require_once __DIR__.'/recaptcha-php/recaptchalib.php';

return config('recaptcha.public') && config('recaptcha.private');