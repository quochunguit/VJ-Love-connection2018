<?php

namespace Contenttype\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContenttypeForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_contenttype');
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
                'id' => 'contenttype_title'
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'type' => 'text',
                'class'=>'required form-control',
                'id' => 'contenttype_type'
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
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getStatus(),
                'class'=>' form-control'
            )
        ));

       $this->add(array(
            'name' => 'group',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->contentTypeGroupKeyVal(),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'content_type_group'
            ),
        ));

       $this->add(array(
            'name' => 'ordering',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'ordering'
            ),
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
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter title'
                                    ),
                                ),
                            )
                        ),
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'type',
                        'required' => false,
                    )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'description',
                        'required' => false,
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'status',
                        'required' => false,
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'group',
                        'required' => false,
                    )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'ordering',
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