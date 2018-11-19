<?php

namespace Setting\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SiteInforForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_siteinfo');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    private function addElements() {


        $this->add(array(
            'name' => 'sitename',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'name'
            ),
        ));

        $this->add(array(
            'name' => 'sitestatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('1' => 'Online', '0' => 'Offline'),
                'class' => ' form-control'
            )
        ));


        $this->add(array(
            'name' => 'ga',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'metakeyword',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'metadescription',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'module',
            'attributes' => array(
                'type' => 'hidden',
                'value' => 'core',
                'id' => 'module',
                'class' => ' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'group',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'group',
                'value' => 'core',
                'class' => ' form-control'
            ),
        ));
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

          
            $inputFilter->add($factory->createInput(array(
                        'name' => 'sitename',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter name'
                                    ),
                                ),
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'sitestatus',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter value'
                                    ),
                                ),
                            )
                        )
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'ga',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'metakeyword',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'metadescription',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'module',
                        'required' => true,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'group',
                        'required' => true,
            )));


            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}
