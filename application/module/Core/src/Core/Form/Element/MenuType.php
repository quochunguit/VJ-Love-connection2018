<?php
namespace Core\Form\Element;
use Zend\Form\Element\Select;

class MenuType extends Select {
    
    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            
            $data = $this->serviceLocator->get('Menu\Model\MenuAdmin')->getAllItemsToKeyVal(array(), array('key' => 'type', 'value' => 'title'));
            
            $this->setValueOptions($data);
        }
    }
  
   

}


