<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class TreePage extends Select {

    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];

            $parentOptions = $this->getServiceLocator()->get('PostTreeService')->getTreeOptions();

            $this->setValueOptions($parentOptions);
        }
    }

}

