<?php

namespace Category\Front\Block\Category;

use Core\Form\Element\ParamsFieldset;

class CategoryParams extends ParamsFieldset {

    function addElements() {
        
        $this->add(array(
            'name' => 'template',
            'options' => array('label' => 'Template'),
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'template'
            ),
        ));
       
        $this->add(array(
            'name' => 'category',
            'type' => 'Core\Form\Element\TreeCategory',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'label' => 'Categories'
            ),
            'attributes' => array(
                'multiple' => true,
                'size' => '10',
                'class' => ' form-control'
            ),
        ));
       
    }

}
