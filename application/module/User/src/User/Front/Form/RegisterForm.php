<?php

namespace User\Front\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;
use Core\Form\CoreForm;

class RegisterForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;
    protected $emailExistValidator;

    public function __construct() {
        parent::__construct('register');
        $this->setAttribute('method', 'post');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    function addElements() {

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
                'class' => 'required'
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required email'
            ),
        ));
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
               
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
               
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'class' => 'required minlength',
                'minlength' => '6',
                'id' => 'password'
            ),
        ));
        $this->add(array(
            'name' => 'cfpassword',
            'attributes' => array(
                'type' => 'password',
                'class' => 'required minlength',
                'minlength' => '6',
                'id' => 'cfpassword',
                'equalTo'=>'#password'
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Register',
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
                        'name' => 'id',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Name can not be empty.',
                                    ),
                                ),
                            ),
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 255,
                                ),
                            ),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'email',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Email can not be empty.',
                                    ),
                                ),
                            ),
                            array(
                                'name' => 'EmailAddress',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\EmailAddress::INVALID => 'Invalid email address'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Core\Validator\UniqueEmail',
                                'options' => array(
                                    'messages' => array(
                                        \Core\Validator\UniqueEmail::EMAIL_EXISTS=> 'Email exists, please enter another email.'
                                    ),
                                    'serviceLocator'=>  $this->getServiceLocator()
                                ),
                            )
                        ),
            )));
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Password can not be empty.',
                                    ),
                                ),
                            ),
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 6,
                                    'max' => 20,
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Confirm password can not be empty.',
                                    ),
                                ),
                            ),
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 6,
                                    'max' => 20,
                                ),
                            ),
                            array(
                                'name' => 'identical',
                                'options' => array(
                                    'token' => 'password'
                                )
                            ),
                        ),
            )));
             $inputFilter->add($factory->createInput(array(
                        'name' => 'address',
                        'required' => false,
            )));
              $inputFilter->add($factory->createInput(array(
                        'name' => 'phone',
                        'required' => false,
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}