<?php

namespace Content\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class FitnessController extends FrontController {

	public function overviewAction(){
        $this->setMetaData(array(), $this->translate('OVERVIEW'));
        $this->layout()->setVariables(array('parent_page'=>'fitness', 'page'=>'fitness_overview'));

		$params = $this->getParams();
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $promotions = $postModel->getByOptions('fitness_overviewpromotions',0, $curlang);
        $services = $postModel->getByOptions('fitness_overviewservices',0, $curlang);
        $facilities = $postModel->getByOptions('fitness_overviewfacilities',0, $curlang);

        return new ViewModel(array(
            'promotions'   => $promotions,
            'services'=>$services,
            'facilities'=>$facilities
        ));
	}

    public function servicedetailAction(){
        $params = $this->getParams();
        $id = $params['id'];
        $slug = $params['slug'];
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $item = $postModel->getByIdAndSlug($id, $slug, $curlang);

        $pageTitle = $item['title'] ? $item['title'] : $this->translate('SERVICES');
        $this->setMetaData(array(), $pageTitle);
        $this->layout()->setVariables(array('parent_page'=>'fitness', 'page'=>'fitness_overview'));
        return new ViewModel(array(
            'item'   => $item    
        ));
    }

    public function promotiondetailAction(){
        $params = $this->getParams();
        $id = $params['id'];
        $slug = $params['slug'];
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $item = $postModel->getByIdAndSlug($id, $slug, $curlang);

        $pageTitle = $item['title'] ? $item['title'] : $this->translate('SERVICES');
        $this->setMetaData(array(), $pageTitle);
        $this->layout()->setVariables(array('parent_page'=>'fitness', 'page'=>'fitness_overview'));
        return new ViewModel(array(
            'item'   => $item    
        ));
    }

    //TODO: Continues
	public function galleryAction(){
    	$this->setMetaData(array(), $this->translate('GALLERY'));
        $this->layout()->setVariables(array('parent_page'=>'fitness', 'page'=>'fitness_gallery'));

        $params = $this->getParams();
        $curlang = $this->getLangCode();
        $tab = $params['tab'] ? $params['tab'] : 'facilities';
        switch ($tab) {
            case 'interior':
                $type = 'residential_galleryinterior';
                break;
             case 'sale-events':
                $type = 'residential_gallerysaleevents';
                break;
            default:
                $type = 'residential_galleryfacilities';
                break;
        } 

        $tabList = array(
            array('tab'=>'facilities','title'=>$this->translate('FACILITIES')),
            array('tab'=>'interior','title'=>$this->translate('INTERIOR')),
            array('tab'=>'sale-events','title'=>$this->translate('SALE EVENTS')),
        );

        $postModel = $this->getPostModel();
        $postModel->setContext($type);
        $postModel->setParams($params);
        $postModel->setState('filter.type', $type);
        $postModel->setState('filter.language', $curlang);
        $postModel->setLimit(12);
        $items = $postModel->getItems();
        return new ViewModel(array(
            'tab'=> $tab,
            'tabList'=>$tabList,
            'items'=> $items,
            'paging'=> $postModel->getPaging(),   
        ));
	}

}