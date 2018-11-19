<?php

namespace Content\Admin\Form;

use Core\Form\CoreForm;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class PostForm extends CoreForm implements InputFilterAwareInterface {

    protected $inputFilter;

    public function init() {
        parent::init();
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'validate form-horizontal panel ');
       
        $_SESSION['content_type'] = $type = $_GET['type']; //Created session contentype for content
        $this->addElements($_GET['lang'], $type);
    }

    protected function addElements($lang, $type) {
        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $curLang = $lang ? $lang: $factory->adminLanguageContentGet();

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
            'name' => 'subtitle',
            'attributes' => array(
                'type' => 'text',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'subtitle1',
            'attributes' => array(
                'type' => 'text',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'subtitle2',
            'attributes' => array(
                'type' => 'text',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'subtitle3',
            'attributes' => array(
                'type' => 'text',
                'class' => ' form-control'
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
            'name' => 'body',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'body',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'body1',
            'attributes' => array(
                'type' => 'textarea',
                'id' => 'body1',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        //TODO: 1 bai n category
        // $this->add(array(
        //     'name' => 'category',
        //     'type' => 'Core\Form\Element\TreeCategoryby',
        //     'options' => array(
        //         'serviceLocator' => $this->getServiceLocator(),
        //     ),
        //     'attributes' => array(
        //         'multiple' => false,
        //         'size' => '10',
        //         'class' => ' form-control'
        //     ),
        // ));

        $whereLanguage = ' (language = "'.$curLang.'" or language = "*" )';
        switch ($type) {
            case 'retail_brandlist':
                $catsOptions = $factory->getPostByWheres(array('type'=>'retail_brandcategory', $whereLanguage), true);
                $catsOptions[0] = ' -- select --';
                ksort($catsOptions);
                 $this->add(array(
                    'name' => 'category',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $catsOptions,
                    ),
                    'attributes' => array(
                        'class' => 'required form-control'
                    ),
                ));

                $catsOptions = $factory->getPostByWheres(array('type'=>'retail_floorplan', $whereLanguage), true);
                $catsOptions[0] = ' -- select --';
                ksort($catsOptions);
                 $this->add(array(
                    'name' => 'category_multi',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $catsOptions,
                    ),
                    'attributes' => array(
                        'multiple' => true,
                        'size' => '10',
                        'class' => 'required form-control'
                    ),
                ));

                break;

            case 'residential_overviewoverviewinfo':
                $options = $factory->residentialOverviewInfoTypeKeyVal();
                $this->add(array(
                    'name' => 'identity',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options,
                    ),
                    'attributes' => array(
                        'class' => 'form-control'
                    ),
                ));

                break;

            case 'static_content_bannerpage':
                $options = $factory->bannerPageTypeKeyVal();
                $this->add(array(
                    'name' => 'identity',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options,
                    ),
                    'attributes' => array(
                        'class' => 'form-control'
                    ),
                ));

                break;

            case 'static_content_headerhotline':
                $options = $factory->holineTypeKeyVal();
                $this->add(array(
                    'name' => 'identity',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options,
                    ),
                    'attributes' => array(
                        'class' => 'form-control'
                    ),
                ));

                break;

             case 'fitness_overviewservices':
                $catsOptions = $factory->getPostByWheres(array('type'=>'retail_floorplan', $whereLanguage), true);
                $catsOptions[0] = ' -- select --';
                ksort($catsOptions);
                 $this->add(array(
                    'name' => 'category_multi',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $catsOptions,
                    ),
                    'attributes' => array(
                        'multiple' => true,
                        'size' => '10',
                        'class' => 'required form-control'
                    ),
                ));

                break;
            
            case 'retail_promotion':
                $catsOptions = $factory->getPostByWheres(array('type'=>'retail_brandlist', $whereLanguage), true);
                $catsOptions[0] = ' -- select --';
                ksort($catsOptions);
                 $this->add(array(
                    'name' => 'category',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $catsOptions,
                    ),
                    'attributes' => array(
                        'class' => 'required form-control'
                    ),
                ));

                break;

            default:
                $catsOptions = $factory->getCatTreeOptions($curLang);
                $catsOptions[0] = ' -- select --';
                ksort($catsOptions);
                 $this->add(array(
                    'name' => 'category',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $catsOptions,
                    ),
                    'attributes' => array(
                        'class' => 'form-control'
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
                break;
        }

        $this->add(array(
            'name' => 'language',
            'type' => 'Core\Form\Element\Language',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator(),
                'class' => ' form-control'
            ),
            'attributes' => array(
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'lang_group',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'more_info',
            'attributes' => array(
                'type' => 'textarea',
                'class' => ' form-control'
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Core\Form\Element\Status',
            'options' => array(
                'serviceLocator' => $this->getServiceLocator()
            ),
            'attributes' => array(
                'class' => 'form-control'
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

        $this->add(array(
            'name' => 'featured',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('0'=>'No','1'=>'Yes')
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'name' => 'home_is_show',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => array('0'=>'No','1'=>'Yes')
            ),
            'attributes' => array(
                'class' => 'form-control'
            )
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

        $this->add(array(
            'name' => 'delete_large_image',
            'attributes' => array(
                'type' => 'text',
                'id' => 'delete_large_image',
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

        $multiImageName = 'multi_image1';
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
            'name' => 'files',
            'attributes' => array(
                'type' => 'text',
                'id' => 'files',
                'class' => ' form-control'
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
                        'name' => 'body1',
                        'required' => false,
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
                        'name' => 'subtitle',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'subtitle1',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'subtitle2',
                        'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'subtitle3',
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
                        'name' => 'language',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'lang_group',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'more_info',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'category',
                        'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'category_multi',
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
                    'name' => 'ordering',
                    'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
                    'name' => 'featured',
                    'required' => false,
            )));


            $inputFilter->add($factory->createInput(array(
                    'name' => 'home_is_show',
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

            $inputFilter->add($factory->createInput(array(
                        'name' => 'delete_large_image',
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

            $multiImageName = 'multi_image1';
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
                        'name' => 'files',
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
