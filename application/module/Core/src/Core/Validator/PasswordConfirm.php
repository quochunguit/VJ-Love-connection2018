<?php
namespace Core\Validator;
use Zend\Validator\AbstractValidator;
class PasswordConfirm extends AbstractValidator {

    const PASSWORD_NOTMATCH = 'passwordnomath';
    var $passwordField;
    
    protected $messageTemplates = array(
        self::PASSWORD_NOTMATCH => 'Xác nhận mật khẩu không khớp với mật khẩu.'
    );

    public function __construct($passwordField) {
        $this->passwordField = $passwordField;
    }

    public function isValid($value, $context = null) {

        $value = (string) $value;
        $this->setValue($value);

        if (is_array($context)) {
            if (isset($context[$this->passwordField]) && ($value == $context[$this->passwordField])) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->error(self::PASSWORD_NOTMATCH);
        return false;
    }

}

