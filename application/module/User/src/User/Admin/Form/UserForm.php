<?php

namespace User\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UserForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('user');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    public function addElements() {
        $factory = $this->getServiceLocator()->get('ServiceFactory');

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
                'class' => 'required form-control'
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required email form-control'
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
                'id' => 'phone',
                'class'=>' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'identify',
            'attributes' => array(
                'type' => 'text',
                'id' => 'identify',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'id' => 'password',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'cfpassword',
            'attributes' => array(
                'type' => 'password',
                'id' => 'cfpassword',
                'equalTo'=>'#password',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getStatus(),
                
            ),
            'attributes' => array(
                'class'=>' form-control',
             )
        ));
        $this->add(array(
            'name' => 'role',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getGroup(),
                
            ),
            'attributes' => array(
                'class'=>' form-control',
             )
        ));

        // $this->add(array(
        //     'type' => 'Zend\Form\Element\Csrf',
        //     'name' => 'csrf',
        //     'options' => array(
        //         'csrf_options' => array(
        //             'timeout' => 600
        //         )
        //     )
        // ));
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter name'
                                    ),
                                ),
                            )
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter email'
                                    ),
                                ),
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
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'cfpassword',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'phone',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'identify',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'status',
                        'required' => true,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'role',
                        'required' => true,
                            )
            ));

          // $inputFilter->add($factory->createInput(array(
          //               'name' => 'csrf',
          //               'validators' => array(
          //                   array(
          //                       'name' => 'csrf'
          //                   )
          //               )
          //   )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

    public function getErrors() {
        return $this->inputFilter->getInvalidInput();
    }

}
