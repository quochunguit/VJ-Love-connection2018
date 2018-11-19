<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class Status extends Select {

    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            $items = array('1' => 'Active', '0' => 'Inactive');
            $this->setValueOptions($items);
        }
    }

}

?>
