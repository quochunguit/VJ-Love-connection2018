<?php

namespace User\Front\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\NotEmpty;


class AccountForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;
    protected $emailExistValidator;

    public function __construct() {
        parent::__construct('editaccount');
        $this->setAttribute('method', 'post');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    function addElements() {
        
        $authService = $this->getServiceLocator()->get('AuthService');
        $user = $authService->getIdentity();
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'value'=>$user['id']
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
                'readonly' => 'readonly',
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
            'name' => 'bio',
            'attributes' => array(
                'type' => 'textarea',
            ),
        ));


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary'
            ),
        ));
    }

   

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => true,
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
                                        NotEmpty::IS_EMPTY => 'Name can not be empty.',
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
                                        NotEmpty::IS_EMPTY => 'Email can not be empty.',
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
                                        \Core\Validator\UniqueEmail::EMAIL_EXISTS => 'Email exists, please enter another email.'
                                    ),
                                    'serviceLocator' => $this->getServiceLocator()
                                ),
                            )
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'address',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'phone',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'bio',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
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
