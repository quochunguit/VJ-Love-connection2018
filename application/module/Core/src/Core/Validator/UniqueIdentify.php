<?php
namespace Core\Validator;
use Zend\Validator\AbstractValidator;
class UniqueIdentify extends AbstractValidator {

    const IDENTIFY_EXISTS = 'IDENTIFY_EXISTS';
  

    protected $model;
    protected $messageTemplates = array(
        self::IDENTIFY_EXISTS => 'CMND đã tồn tại',
      
    );
    
    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    
  

    public function isValid($value, $context = null) {
        $this->setValue($value);
        
        if ($this->getModel()) {
            $isExist = $this->getModel()->isIdentifyExist($this->value);
            if (!$isExist) {
                return true;
            }
        }
        $this->error(self::IDENTIFY_EXISTS);
        return false;
    }

}

