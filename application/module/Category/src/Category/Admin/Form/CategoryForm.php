<?php

namespace Category\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CategoryForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function __construct() {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
    }

    public function init() {
        parent::init();

        unset($_SESSION['content_type']); //Clear session contentype for content
        $this->addElements($_GET['lang']);
    }

    public function addElements($lang) {
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
                'class' => 'slug required form-control',
                'id' => 'slug'
            ),
        ));
        $this->add(array(
            'name' => 'intro',
            'attributes' => array(
                'type' => 'textarea',
                'class'=>'form-control'
            ),
        ));
        $this->add(array(
            'name' => 'body',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'body',
                'class'=>'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'language',
            'type' => 'Core\Form\Element\Language',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator()
            ),
            'attributes' => array(
                'class'=>'form-control'
            )
        ));

        $this->add(array(
            'name' => 'lang_group',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        
        $parentOptions = $this->getServiceLocator()->get('Category\Admin\Model\Category')->getRootOptions($lang);
        $parentOptions[0] = ' -- select --';
        ksort($parentOptions);
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $parentOptions
            ),
            'attributes' => array(
                'class'=>'form-control'
            )
        ));

        $this->add(array(
            'name' => 'ordering',
            'attributes' => array(
                'type' => 'text',
                'class' => 'ordering form-control',
                'id' => 'ordering'
            )
        ));
        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $factory->getStatus()
            ),
            'attributes' => array(
                'class'=>'form-control'
            )
        ));
        $this->add(array(
            'name' => 'image',
            'type' => '\Core\Form\Element\Media',
            'attributes' => array(
                'data-label' => 'Choose File',
                'data-media-filter' => '*',
                'data-muiltiple' => 'false',
                'data-media-append' => 'files1'
            ),
        ));

        $arrTypeFix = array();
        $contentTypeGroups = $factory->contentTypeGroup();
        if($contentTypeGroups){
            foreach ($contentTypeGroups as $key => $value) {
                $arrTypeFix[$key] = array('label'=>$value['title'],'options'=>$factory->getContentType($value['id'], true));
            }
        }else{
            $arrTypeFix = $factory->getContentType(null, true);
        }

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                    'value_options' =>  $arrTypeFix
                ),
            'attributes' => array(
                    'class'=>'form-control'
                )
            )
        );

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
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
                        'required' => false,
                            )
            ));

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
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'slug',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'intro',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 100,
                                ),
                            ),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'body',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 100,
                                ),
                            ),
                        ),
            )));      

            $inputFilter->add($factory->createInput(array(
                        'name' => 'status',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'language',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'lang_group',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'parent_id',
                        'required' => false,
                            )
            ));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'ordering',
                        'required' => false,
                            )
            ));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'image',
                        'required' => false,
                            )
            ));

             $inputFilter->add($factory->createInput(array(
                        'name' => 'type',
                        'required' => true,
                            )
            ));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'csrf',
                        'validators' => array(
                            array(
                                'name' => 'csrf'
                            )
                        )
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->inputFilter = $inputFilter;
    }

}
