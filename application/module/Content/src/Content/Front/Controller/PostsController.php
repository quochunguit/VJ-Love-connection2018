<?php

namespace Content\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class PostsController extends FrontController {

    // -- Tin tuc --
    public function ruleAction() {

        $postModel = $this->getPostModel();

        $language = $this->getLangCode();
        $type ='t_and_c';
        $postModel->setContext('rule');
        $item = $postModel->getItem(array('type'=>$type,'language'=>$language));
        $item2 = $postModel->getItem(array('type'=>'flight_information','language'=>$language));
        //print_r($news->toArray());die;

        $this->setMetaData(array(), $this->translate('t_ctrans'));
        $this->layout()->setVariables(array('page'=>'rule'));
        return new ViewModel(array(
            'rule'   => $item,
            'flight' => $item2

        ));
    }

    public function aboutAction() {

        $postModel = $this->getPostModel();

        $language = $this->getLangCode();
        $type ='content_about';
        $postModel->setContext('about');
        $item = $postModel->getItem(array('type'=>$type,'language'=>$language));

        $this->setMetaData(array(), $this->translate('About'));
        $this->layout()->setVariables(array('page'=>'about'));
        return new ViewModel(array(
            'about'   => $item,
        ));
    }

    public function flightinformationAction(){
        $postModel = $this->getPostModel();
        $language = $this->getLangCode();
        $type ='flight_information';

        $item = $postModel->getItem(array('type'=>$type,'language'=>$language));
        //print_r($news->toArray());die;

        $this->setMetaData(array(), $this->translate('Flight_Information'));
        $this->layout()->setVariables(array('page'=>'flight'));
        return new ViewModel(array(
            'flightinformation'   => $item

        ));
    }
    // -- end Tin tuc
    //*******************************************************************

}