<?php

namespace User\Front\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;

use Core\Form\CoreForm;

class ChangePasswordForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('changepassword');
        $this->setAttribute('method', 'post');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    function addElements() {
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password',
                'class'=>'required'
            ),
        ));
        $this->add(array(
            'name' => 'cfpassword',
            'attributes' => array(
                'type' => 'password',
                'id' => 'cfpassword',
                'class'=>'required',
                'equalTo'=>'#password'
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Change',
                'id' => 'submitbutton',
                 'class'=>'btn btn-primary'
            ),
        ));
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();




            $inputFilter->add($factory->createInput(array(
                        'name' => 'password',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        NotEmpty::IS_EMPTY => 'Password can not be empty.',
                                    ),
                                ),
                            ),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'cfpassword',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        NotEmpty::IS_EMPTY => 'Confirm password can not be empty.',
                                    ),
                                ),
                            ),
                          
                        ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}