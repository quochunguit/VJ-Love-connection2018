<?php
namespace Core\Form\Element;
use Zend\Form\Element\Select;

class Position extends Select {
    
    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            
            $config = $this->serviceLocator->get('Config');
            
            $this->setValueOptions($config['position']);
        }
    }
  
   

}


