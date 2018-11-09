<?php
namespace Core\Validator;
use Zend\Validator\AbstractValidator;
require_once 'phpcaptcha/php-captcha.inc.php';
class PhpCaptcha extends AbstractValidator {

    const CAPTCHA_INCORRECT = '';

    protected $messageTemplates = array(
        self::CAPTCHA_INCORRECT => 'Mã bảo mật không đúng.'
    );

    public function __construct() {
        
    }

    public function isValid($value, $context = null) {

        $value = strtoupper($value);
        $this->setValue($value);
        if ((!empty($_SESSION[CAPTCHA_SESSION_ID])) && $this->value == $_SESSION[CAPTCHA_SESSION_ID]) {
            unset($_SESSION[CAPTCHA_SESSION_ID]);
            return true;
        }
        $this->error(self::CAPTCHA_INCORRECT);
        return false;
    }

}

