<?php

namespace Contest\Admin\Form;

use Core\Form\CoreForm;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContestForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct('n_contest');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'validate form-horizontal panel ');
        
    }
    public function init() {
        parent::init();
        $this->addElements('vi','contest');
                
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
            'name' => 'slug',
            'attributes' => array(
                'type' => 'text',
                'id' => 'slug',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'descriptions',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'descriptions',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'type' => 'hidden',
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
            'name' => 'images',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'images'
            ),
        ));

        $this->add(array(
            'name' => 'featured',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('0'=>'No','1'=>'Yes') ,
                'class'=>'form-control'
            )
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
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'text',
                'id' => 'user_id',
                'class' => 'required form-control'
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
                        'name' => 'slug',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'type',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                    'name' => 'status',
                    'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                    'name' => 'featured',
                    'required' => false,
            )));



            $inputFilter->add($factory->createInput(array(
                        'name' => 'delete_image',
                        'required' => false,
            )));

            //----------------------------------


            $inputFilter->add($factory->createInput(array(
                        'name' => 'user_id',
                        'required' => true,
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