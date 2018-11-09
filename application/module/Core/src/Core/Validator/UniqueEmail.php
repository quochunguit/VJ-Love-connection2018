<?php

namespace Core\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Stdlib\ArrayUtils;

class UniqueEmail extends AbstractValidator {

    const EMAIL_EXISTS = 'emailExists';

    protected $model;
    protected $messageTemplates = array(
        self::EMAIL_EXISTS => "Email exists"
    );
    protected $serviceLocator;

    public function __construct($options = null) {

        if ($options instanceof \Traversable)
            $options = ArrayUtils::iteratorToArray($options);

        if (!isset($options['serviceLocator']))
            throw new \InvalidArgumentException(__CLASS__ . ' requires the option serviceLocator.');
        $ServiceManagerInstance = 'Zend\ServiceManager\ServiceManager';
        if (!($options['serviceLocator'] instanceof $ServiceManagerInstance))
            throw new \InvalidArgumentException(__CLASS__ . ' expects the option serviceLocator to be an instance of Zend\ServiceManager\ServiceManager.');

        $this->serviceLocator = $options['serviceLocator'];

        parent::__construct(is_array($options) ? $options : null);
    }

    public function getModel() {
        return $this->getServiceLocator()->get('User\Front\Model\User');
    }

    public function setModel($model) {

        $this->model = $model;
    }

    public function isValid($value, $context = null) {
        $this->setValue($value);

        $userId = $context['id'];
        if ($this->getModel()) {
            $user = $this->getModel()->getItem(array('email' => $this->value));

            if (!$user) {
                return true;
            } elseif ($user && $user['id'] == $userId) {
                return true;
            }
        }
        $this->error(self::EMAIL_EXISTS);

        return false;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}
