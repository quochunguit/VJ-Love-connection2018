<?php

namespace Setting\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SettingForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function init() {
        parent::init();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
        $this->addElements();
    }

    

    protected function addElements() {
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'name'
            ),
        ));


        $this->add(array(
            'name' => 'value',
            'attributes' => array(
                'type' => 'textarea',
                'class' => 'form-control',
                'id'=>'value'
            ),
        ));
        $this->add(array(
            'name' => 'desc',
            'attributes' => array(
                'type' => 'textarea',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'module',
            'attributes' => array(
                'type' => 'text',
                'id' => 'module',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'group',
            'attributes' => array(
                'type' => 'text',
                'id' => 'group',
                'class'=>'required form-control'
            ),
        ));
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'name',
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
                        'name' => 'value',
                        'required' => false,
                        // 'validators' => array(
                        //     array(
                        //         'name' => 'NotEmpty',
                        //         'options' => array(
                        //             'messages' => array(
                        //                 \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter value'
                        //             ),
                        //         ),
                        //     )
                        // )
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'desc',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'module',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'group',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter group'
                                    ),
                                ),
                            )
                        )
            )));


            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}
