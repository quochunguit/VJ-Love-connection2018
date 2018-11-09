<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class Language extends Select {

    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            $factory = $this->serviceLocator->get('ServiceFactory');
       
            $langs = $factory->getLanguage(true, array('status'=>1));
            $langs['*'] = ' -- Select --';
            ksort($langs);
            //$items = array('*' => 'All', 'vi-VN' => 'Tiếng Việt', 'en-GB' => 'English');

            $this->setValueOptions($langs);
        }
    }

}

?>
