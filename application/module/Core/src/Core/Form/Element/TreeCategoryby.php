<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class TreeCategoryby extends Select {
    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            
            $categoryModel = $this->getServiceLocator()->get('Category\Admin\Model\Category');
            $items = $categoryModel->getCategoryByParent(7);
            $cat = array();
            $items=$items['child'];
           
            foreach ($items as  $value) 
            {
               $cat[$value['id']]=$value['title'];
            }
            
            $this->setValueOptions($cat);
        }
    }
}

?>