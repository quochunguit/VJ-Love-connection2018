<?php

namespace Core\Form\Element;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Fieldset;

abstract class ParamsFieldset extends Fieldset implements ServiceLocatorAwareInterface {

    protected $serviceLocator;

    public function init() {
        parent::init();
        $this->setObject(new \ArrayObject());
        $this->setHydrator(new \Zend\Stdlib\Hydrator\ObjectProperty());
        $this->addElements();

    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    abstract function addElements();
}
