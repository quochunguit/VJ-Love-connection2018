<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class TreeCategory extends Select {
    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            
            $categoryModel = $this->getServiceLocator()->get('Category\Admin\Model\Category');
            $items = $categoryModel->getTreeOptions();
            
            $this->setValueOptions($items);
        }
    }
}

?>