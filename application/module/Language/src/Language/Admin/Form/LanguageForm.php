<?php

namespace Language\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class LanguageForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_language');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
    }

    public function init() {
        parent::init();
        $this->addElements();
    }

    private function addElements() {
        $factory = $this->getServiceLocator()->get('ServiceFactory');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'title'
            ),
        ));

        $this->add(array(
            'name' => 'title_short',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'title_short'
            ),
        ));

        $this->add(array(
            'name' => 'slug',
            'attributes' => array(
                'type' => 'text',
                'id' => 'slug',
                'class' => 'required form-control',
            ),
        ));
        $this->add(array(
            'name' => 'lang_code',
            'attributes' => array(
                'type' => 'text',
                'id' => 'lang_code',
                'class' => 'required form-control',
            ),
        ));
        $this->add(array(
            'name' => 'ordering',
            'attributes' => array(
                'type' => 'text',
                'id' => 'ordering',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'description',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'is_default',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('0' => 'No', '1' => 'Yes')
            ),
            'attributes' => array(
                'class'=>' form-control'
            )
        ));
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getStatus()
            ),
            'attributes' => array(
                'class'=>' form-control'
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
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'title',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter title'
                                    ),
                                ),
                            )
                        )
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'title_short',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'slug',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter slug'
                                    ),
                                ),
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'lang_code',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter language code'
                                    ),
                                ),
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'ordering',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'description',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'is_default',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'status',
                        'required' => false,
            )));

            // $inputFilter->add($factory->createInput(array(
            //             'name' => 'csrf',
            //             'validators' => array(
            //                 array(
            //                     'name' => 'csrf'
            //                 )
            //             )
            // )));


            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}
