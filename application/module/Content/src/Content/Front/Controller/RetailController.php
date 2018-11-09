<?php

namespace Content\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class RetailController extends FrontController {

    public function promotionAction() {
        $params = $this->getParams();
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $items = $postModel->getByOptions('retail_promotion',0, $curlang);

        $this->setMetaData(array(), $this->translate('PROMOTION'));
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_promotion'));
        return new ViewModel(array(
            'paramIndexKey'=>$params['id'].$params['slug'],
            'items'   => $items    
        ));
    }

    public function eventAction() {
        $params = $this->getParams();
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $items = $postModel->getByOptions('retail_event',0, $curlang);

        $this->setMetaData(array(), $this->translate('EVENT'));
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_event'));
        return new ViewModel(array(
            'paramIndexKey'=>$params['id'].$params['slug'],
            'items'   => $items    
        ));
    }

    public function brandAction() {
        $params = $this->getParams();
        $keyword = $params['k'];
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();

        $categoryListFix = array();

        if($keyword){
            $brandList = $postModel->getByOptionsSearch('retail_brandlist',0, $curlang, $keyword);
            $categoryListFix[] = array(
                'title'=>$this->translate('Search Results').': <span style="background-color:yellow; padding: 2px;">'.$keyword.'</span>',
                'brandList'=>$brandList
            );
        }else{
            $categoryList = $postModel->getByOptions('retail_brandcategory',0, $curlang);
            if($categoryList){
                foreach ($categoryList as $key => $value) {
                    $brandList = $postModel->getByOptions('retail_brandlist',0, $curlang, array('category'=>$value['id']));

                    if($brandList){
                        $value['brandList'] = $brandList;
                        $categoryListFix[] = $value;
                    } 
                }
            }
        }
        //print_r($categoryListFix); die;

        $this->setMetaData(array(), $this->translate('BRANDS'));
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_brand'));
        return new ViewModel(array(
            'keyword'=>$keyword,
            'categoryList'   => $categoryListFix
        ));
    }

    public function branddetailAction() {
        $params = $this->getParams();
        $id = $params['id'];
        $slug = $params['slug'];
        $curlang = $this->getLangCode();

        $postModel = $this->getPostModel();
        $item = $postModel->getByIdAndSlug($id, $slug, $curlang);

        $pageTitle = $item['title']? $item['title'] : $this->translate('BRANDS');
        $this->setMetaData(array(), $pageTitle);
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_brand'));

        $promotion = $postModel->getByOptions($type = 'retail_promotion', 1, $curlang, array('category'=>$item['id']));
        //print_r($item); die;

        return new ViewModel(array(            
            'item'   => $item,
            'promotion'=>$promotion
        ));
    }


    /*Floor plan*/
    public function floorplanAction() {
        $params = $this->getParams();
        $curlang = $this->getLangCode();

        $factory = $this->getFactory();
        $postModel = $this->getPostModel();
        
        $items = $postModel->getByOptions('retail_floorplan',0, $curlang);
        $itemsFix = $factory->floorplanGetBrands($items);

        $this->setMetaData(array(), $this->translate('FLOORPLAN'));
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_floorplan'));
        return new ViewModel(array(
            'items'   => $itemsFix    
        ));
    }
    /*Floor plan*/


    /*Gallery*/
    public function galleryAction() {
        /*Load paging ajax */
        if($this->isAjax()){
            $params = $this->getParams('post');
            $tabContentHtml = $this->getStrGalleryItemsByType($params);
            $this->returnJsonAjax(array('status'=>true,'message'=>'OK','tabContentHtml'=>$tabContentHtml));    
        } 

        /*Load page */
        $this->setMetaData(array(), $this->translate('GALLERY | SOCIAL MEDIA'));
        $this->layout()->setVariables(array('parent_page'=>'retail', 'page'=>'retail_gallery'));
        $params = $this->getParams();
        $tabList = $this->galleryGetTabList();
        return new ViewModel(array(
            'tabList'=>$tabList
        ));
    }

    private function galleryGetTabList(){
        return array(

            array('tab'=>'shopping-mall', 'type'=>'retail_galleryshoppingmall', 'title'=>$this->translate('SHOPPING MALL'), 'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'retail_galleryshoppingmall'))),

            array('tab'=>'event', 'type'=>'retail_galleryevent', 'title'=>$this->translate('EVENTS'),'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'retail_galleryevent'))),

            array('tab'=>'social-media', 'type'=>'retail_gallerysocialmedia', 'title'=>$this->translate('SOCIAL MEDIA'),'tabContentHtml'=> $this->getStrGalleryItemsByType(array('page'=>1,'type'=>'retail_gallerysocialmedia'))),
        );
    }

    private function getStrGalleryItemsByType($params, $limit = 12){
        $curlang = $this->getLangCode();
        $type = $params['type'] ? $params['type'] : 'retail_galleryshoppingmall';

        $pagingParams = array(
                            'ajax_url'=>'/retail-action/gallery',
                            'gallery_type'=>$type,
                            'el_append'=>'retail-gallery-view'
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

        $viewModel->setTemplate('content/retail/partials/gallerytabitems');
        $renderer = $this->getViewRender();
        return $renderer->render($viewModel);
    }
    /*End Gallery*/

}