<?php

namespace Contest\Admin\Form;

use Core\Form\CoreForm;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContestWeekForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_contest');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'validate form-horizontal panel ');
        
    }
    public function init() {
        parent::init();
        $this->addElements();
                
    }

    protected function addElements($lang, $type) {
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
            'name' => 'intro',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control',
                'name' => 'intro',
                'rows'  => '5'
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Core\Form\Element\Status',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'class' => ' form-control'
            )
        ));  

       $this->add(array(
            'name' => 'ordering',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'ordering'
            ),
        )); 

        //image crop
        $this->add(array(
            'name' => 'image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'x_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'x_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'y_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'y_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'w_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'w_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'h_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'h_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'delete_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'delete_image',
                'class' => ' form-control'
            ),
        ));
        //-------------

        $this->add(array(
            'name' => 'from_date',
            'attributes' => array(
                'type' => 'text',
                'id' => 'from_date',
                'class' => ' form-control',
                'value' => date('Y-m-d H:i:s')
            ),
        )); 

        $this->add(array(
            'name' => 'to_date',
            'attributes' => array(
                'type' => 'text',
                'id' => 'to_date',
                'class' => ' form-control',
                'value' => date('Y-m-d H:i:s')
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
                        'name' => 'intro',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                    'name' => 'status',
                    'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                    'name' => 'ordering',
                    'required' => false,
            )));

           //-- image ------
            $inputFilter->add($factory->createInput(array(
                        'name' => 'image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'x_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'y_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'w_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'h_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'delete_image',
                        'required' => false,
            )));

            //----------------------------------

            $inputFilter->add($factory->createInput(array(
                        'name' => 'from_date',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'to_date',
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