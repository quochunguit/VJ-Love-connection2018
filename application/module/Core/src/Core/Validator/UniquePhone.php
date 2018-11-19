<?php

namespace Core\Validator;

use Zend\Validator\AbstractValidator;

class UniquePhone extends AbstractValidator {

    const PHONE_EXISTS = 'PHONE_EXISTS';
    const PHONE_WRONG = 'PHONE_WRONG';

    protected $model;
    protected $messageTemplates = array(
        self::PHONE_EXISTS => 'Số điện thoại "%value%" đã tồn tại, vui lòng chọn số khác',
        self::PHONE_WRONG => 'Số điện thoại "%value%" không hợp lệ, vui lòng chọn số khác',
    );

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function isValid($value, $context = null) {
        $this->setValue($value);
        $phone = $this->value;
        $prefix = substr($phone, 1, 1);


        if ($prefix == '0') {
            $this->error(self::PHONE_WRONG);
            return false;
        }

        if ($this->getModel()) {
            $isExist = $this->getModel()->isPhoneExist($this->value);
            if (!$isExist) {
                return true;
            }
        }
        $this->error(self::PHONE_EXISTS);
        return false;
    }

}

