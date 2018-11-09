<?php

namespace Content\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class PostsController extends FrontController {

    // -- Tin tuc --
    public function newsAction() {

        $postModel = $this->getPostModel();

        $language = $this->getLangCode();
        $type ='news';

        $params = $this->getParams();
        if($params['filterOption']){
            $_SESSION['newsFilterOption'] = $params['filterOption'];
            $postModel->setState('order.field','created');
            $postModel->setState('order.direction',$params['filterOption']);
        }
         if(!empty($_SESSION['newsFilterOption'])){
            $postModel->setState('order.field','created');
            $postModel->setState('order.direction',$_SESSION['newsFilterOption'] );
        }

        $postModel->setContext('tintuc');
        $postModel->setParams($params);
        $postModel->setState('filter.type',$type);
        $postModel->setState('filter.language',$language);
        
        $postModel->setLimit(8);
        $news = $postModel->getItems();

        $this->setMetaData(array(), $this->translate('Tin tuc'));
        $this->layout()->setVariables(array('page'=>'tin_tuc', 'sub_menu'=>'tin_tuc'));
        return new ViewModel(array(
            'news'   => $news->toArray(),
            'paging' => $postModel->getPaging(),       
        ));
    }

    public function newsviewAction(){
        $postModel = $this->getPostModel();
        $language = $this->getLangCode();
        $id = $this->params()->fromRoute('id');

        $type ='news';
        $news= $postModel->getByIdAndSlug($id, null, $type);
       
        if($news){ //Exist news
            $this->setMetaData(array(), trim($news['title']));
            $this->layout()->page='tin_tuc';
           
            $otherNews = $postModel->getByOptions($type,5,$language,array('template'=>self::TEMPLATE,'id <> ?'=>$id,'featured'=>$valFeature));

            return new ViewModel(array(
                'news' => $news,
                'otherNews' =>$otherNews
            ));
        }else{
            $this->redirectToRoute('news-index-'.$language);
        }
    }
    // -- end Tin tuc
    //*******************************************************************

}