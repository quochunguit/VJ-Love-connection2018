<?php

namespace Content\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class ResidentialController extends FrontController {

	public function overviewAction(){
        $this->setMetaData(array(), $this->translate('OVERVIEW'));
        $this->layout()->setVariables(array('parent_page'=>'residential', 'page'=>'residential_overview'));

		$params = $this->getParams();
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();

        $videos = $postModel->getByOptions('residential_overviewvideo',0, $curlang);

        /*Over view info*/
        $overview = $postModel->getByOptions('residential_overviewoverviewinfo',1, $curlang, array('identity'=>'overview'));
        $introduction = $postModel->getByOptions('residential_overviewoverviewinfo',1, $curlang, array('identity'=>'introduction'));
        $concept = $postModel->getByOptions('residential_overviewoverviewinfo',1, $curlang, array('identity'=>'concept'));
        $salePolicy = $postModel->getByOptions('residential_overviewoverviewinfo',1, $curlang, array('identity'=>'sale_policy'));
        $overviewInfo = array(
            'overview'=>$overview,
            'introduction'=>$introduction,
            'concept'=>$concept,
            'salePolicy'=>$salePolicy
        );
        /*End Over view info*/

        $floorplans = $postModel->getByOptions('residential_overviewfloorplan',0, $curlang);
        $amenities =  $postModel->getByOptions('residential_overviewamenities',1, $curlang);

        return new ViewModel(array(
            'videos'   => $videos,
            'overviewInfo'=>$overviewInfo,
            'floorplans'=>$floorplans,
            'amenities'=>$amenities
        ));
	}

    /*Gallery*/
    public function galleryAction() {
        /*Load paging ajax */
        if($this->isAjax()){
            $params = $this->getParams('post');
            $tabContentHtml = $this->getStrGalleryItemsByType($params);
            $this->returnJsonAjax(array('status'=>true,'message'=>'OK','tabContentHtml'=>$tabContentHtml));    
        } 

        /*Load page */
        $this->setMetaData(array(), $this->translate('GALLERY'));
        $this->layout()->setVariables(array('parent_page'=>'residential', 'page'=>'residential_gallery'));
        $params = $this->getParams();
        $tabList = $this->galleryGetTabList();
        return new ViewModel(array(
            'tabList'=>$tabList
        ));
    }

    private function galleryGetTabList(){
        return array(

            array('tab'=>'facilities', 'type'=>'residential_galleryfacilities', 'title'=>$this->translate('FACILITIES'), 'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'residential_galleryfacilities'))),

            array('tab'=>'interior', 'type'=>'residential_galleryinterior', 'title'=>$this->translate('INTERIOR'),'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'residential_galleryinterior'))),

            array('tab'=>'sale-events', 'type'=>'residential_gallerysaleevents', 'title'=>$this->translate('SALE EVENTS'),'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'residential_gallerysaleevents'))),
        );
    }

    private function getStrGalleryItemsByType($params, $limit = 12){
        $curlang = $this->getLangCode();
        $type = $params['type'] ? $params['type'] : 'residential_galleryfacilities';

        $pagingParams = array(
                            'ajax_url'=>'/residential-action/gallery',
                            'gallery_type'=>$type,
                            'el_append'=>'residential-gallery-view'
                        );

        $postModel = $this->getPostModel();
        $postModel->setContext($type);
        $postModel->setParams($params);
        $postModel->setState('filter.type',$type);
        $postModel->setState('filter.language', $curlang);
        $postModel->setLimit($limit);
        $items = $postModel->getItems();

        $viewModel = new ViewModel(array(
            'items'=> $items,
            'paging'=> $postModel->getPaging(),   
            'pagingParams'=>$pagingParams,
        ));

        $viewModel->setTemplate('content/residential/partials/gallerytabitems');
        $renderer = $this->getViewRender();
        return $renderer->render($viewModel);
    }
    /*End Gallery*/

}