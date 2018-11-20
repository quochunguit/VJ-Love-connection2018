<?php

namespace Core\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\FormInterface;

abstract class CoreForm extends Form implements ServiceLocatorAwareInterface {

    protected $service;
    public $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
         $this->serviceLocator = $serviceLocator->getServiceLocator();
        //$this->serviceLocator = $serviceLocator; //TODO:Dev vendor_php_5_3_3.zip*/
    }

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED) {

        return parent::bind($object, $flags);
    }

}
