<?php

namespace Vote\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class VoteForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function init() {
        parent::init();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
        $this->addElements();
    }

    protected function addElements() {
        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'identity',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'identity'
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
            'name' => 'intro',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control'
            ),
        ));
        $this->add(array(
            'name' => 'body',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'body',
                'class' => ' form-control'
            ),
        ));

       
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Core\Form\Element\TreePage',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'class' => 'form-control'
            )
        ));


       $this->add(array(
            'name' => 'subtype',
            'type' => 'Core\Form\Element\SubType',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'class' => ' form-control'
            )
        ));

        $this->add(array(
            'name' => 'media_link',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'title'
            ),
        ));

        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'title'
            ),
        ));

        // $this->add(array(
        //     'name' => 'image',
        //     'type' => '\Core\Form\Element\Media',
        //     'attributes' => array(
        //         'data-label' => 'Choose Image',
        //         'data-media-filter' => 'jpg,gif,png,jpeg,bmp',
        //         'data-muiltiple' => 'false',
        //         'data-media-append' => 'files1',
        //         'data-media-folder'=>'/images'
        //     ),
        // ));

        // $this->add(array(
        //     'name' => 'large_image',
        //     'type' => '\Core\Form\Element\Media',
        //     'attributes' => array(
        //         'data-label' => 'Choose Image',
        //         'data-muiltiple' => 'false',
        //         'data-media-filter' => 'jpg,gif,png,jpeg,bmp',
        //         'data-media-append' => 'files2',
        //         'data-media-folder'=>'/images'
        //     ),
        // ));

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

        //-------------
        //---- large image crop----   

        $this->add(array(
            'name' => 'large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'large_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'x_large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'x_large_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'y_large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'y_large_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'w_large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'w_large_image',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'h_large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'h_large_image',
                'class' => ' form-control'
            ),
        ));

        //--Multi image
        $multiImageName = 'multi_image';
        $this->add(array(
            'name' => $multiImageName,
            'attributes' => array(
                'type' => 'text',
                'id' => $multiImageName,
                'class' => ' form-control'
            ),
        ));
        $this->add(array(
            'name' => $multiImageName . '_caption',
            'attributes' => array(
                'type' => 'text',
                'id' => $multiImageName . '_caption',
                'class' => ' form-control'
            ),
        ));
        //--End Multi image  

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));


        $catID = @$_GET['cat'];
        if(!empty($catID)){
        	$this->add(array(
        			'name' => 'category',
        			'type' => 'Core\Form\Element\TreeCategoryByType',
        			'options' => array(
        					'serviceLocator' => $this->getServiceLocator(),
        			),
        			'attributes' => array(
        					'multiple' => true,
        					'size' => '5',
        					'class' => ' form-control',
        					'value'	=> $catID
        			),
        	));
        	
        }else{
        	$this->add(array(
        			'name' => 'category',
        			'type' => 'Core\Form\Element\TreeCategory',
        			'options' => array(
        					'serviceLocator' => $this->getServiceLocator(),
        			),
        			'attributes' => array(
        					'multiple' => true,
        					'size' => '5',
        					'class' => ' form-control'
        			),
        	));
        	
        }
        

        $this->add(array(
            'name' => 'language',
            'type' => 'Core\Form\Element\Language',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'class' => ' form-control'
            )
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
            'name' => 'type_award',
            'type' => 'select',
            'options' => array(
                        'empty_option' => 'Choose Award type',
                        'value_options' => array(
                            'day'=>'Giải ngày',
                            'week'=>'Giải tuần',
                            'final'=>'Giải chung cuộc'
                        )
                )
        ));
        
        
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => true,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'identity',
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
                        'name' => 'body',
                        'required' => false,
                        // 'filters' => array(
                        //     array('name' => 'StripTags'),
                        //     array('name' => 'StringTrim'),
                        // ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                ),
                            ),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'parent_id',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'slug',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'intro',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => false,
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
                        'name' => 'category',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'type',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'subtype',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'media_link',
                        'required' => false,
            )));

          $inputFilter->add($factory->createInput(array(
                        'name' => 'user_id',
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

            //----------------------------------
            //----- large image ---------------

            $inputFilter->add($factory->createInput(array(
                        'name' => 'large_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'x_large_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'y_large_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'w_large_image',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'h_large_image',
                        'required' => false,
            )));

            //----- end large image -------
            //--Multi image
            $multiImageName = 'multi_image';
            $inputFilter->add($factory->createInput(array(
                        'name' => $multiImageName,
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => $multiImageName . '_caption',
                        'required' => false,
            )));
            //--End Multi image
            $inputFilter->add($factory->createInput(array(
                        'name' => 'type_award',
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
