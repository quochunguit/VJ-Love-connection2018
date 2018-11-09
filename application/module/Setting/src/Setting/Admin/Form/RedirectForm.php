<?php

namespace Setting\Admin\Form;

use Core\Form\CoreForm;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RedirectForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_redirect');
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
            'name' => 'source',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'title'
            ),
        ));
        $this->add(array(
            'name' => 'dst',
            'attributes' => array(
                'type' => 'text',
                'class' => 'required form-control',
                'id' => 'dst'
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type' => 'textarea',
                'class'=>' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'language',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getLanguage(),
                'class'=>' form-control'
            )
        ));
        $this->add(array(
            'name' => 'header',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('301'=>301, 302=>302, 303=>303, 304=>304, 305=>305, 307=>307),
                'class'=>' form-control'
            )
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getStatus(),
                'class'=>' form-control'
            )
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
                        'name' => 'source',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter from url'
                                    ),
                                ),
                            )
                        )
            )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'dst',
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
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter url redirect to'
                                    ),
                                ),
                            )
                        )
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
                        'name' => 'status',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'language',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'header',
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
