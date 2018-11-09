<?php

namespace Core\Service;

class Factory {

    private $serviceLocator;

    public function __construct($serviceLocator = '') {
        $this->serviceLocator = $serviceLocator;
    }

    /*--****************** ADMIN ****************---*/
    public function getStatus() {
        return array('1' => 'Active', '0' => 'Inactive');
    }

    public function getGroup() {
        $groupModel = $this->getServiceLocator()->get('User\Admin\Model\Group');
        
        $items = $groupModel->getAllItemsToKeyVal(array(), array('key' => 'id', 'value' => 'title'));
        unset($items[9]);
        return $items;
    }

    public function getLanguage($isGetkeyVal = true, $wheres=array()) {
        $langModel = $this->getServiceLocator()->get('Language\Admin\Model\Language');
        if($isGetkeyVal){
            $items = $langModel->getAllItemsToKeyVal($wheres, array('key' => 'lang_code', 'value' => 'title'));
        }else{
            $items = $langModel->getAllItemsToArray($wheres);
        }
        return $items;    
    }

    public function getLanguageByCode($code='') {
        $item = array();
        $code = $code ? $code : $this->adminLanguageContentGet();
        if($code){
            $langModel = $this->getServiceLocator()->get('Language\Admin\Model\Language');
            $item = $langModel->getItem(array('lang_code'=>$code));
        }
        
        return $item;    
    }

    //--Check add language
    public function checkAddLanguage($item = array(), $type='post'){

        if($item['language'] == '*'){
            return '---';
        }

        $strLinkAction = '';
        $langs = $this->getLanguage(false, array('status'=>1));
        switch ($type) {
            case 'category':
                $model = $this->getServiceLocator()->get('Category\Admin\Model\Category');
                $routeAdd = BASE_URL.'/admin/category/add?lang=';    
                $routeView = BASE_URL.'/admin/category/edit/';        
                break;
            
            default: //Post
                $model = $this->getServiceLocator()->get('Content\Admin\Model\Post');
                $routeAdd = BASE_URL.'/admin/post/add/'.$item['type'].'?type='.$item['type'].'&lang='; 
                $routeView = BASE_URL.'/admin/post/edit/'; 
                break;
        }

        if($langs){
            foreach ($langs as $key => $value) {
                if($item['language'] != $value['lang_code']){
                    $itemCheck = $model->getItem(array('language'=>$value['lang_code'],'lang_group'=>$item['lang_group']));
                    if(!$itemCheck){
                        $linkAdd = $routeAdd . $value['lang_code'] . '&lang_group=' . $item['lang_group'];
                        $strLinkAction  .= '<a target="_blank" href="'. $linkAdd .'" title="'.$value['description'].'">'.$value['title'].'</a>'.' | ';
                    }else{
                        $type = $itemCheck['type'] ? '/?type=' . $itemCheck['type'] : '';
                        $linkView = $routeView .$itemCheck['id'] .  $type;
                        $strLinkAction  .= '<a target="_blank" href="'. $linkView .'" title="'.$value['description'].'">'.$value['title'].'</a>'.' | ';
                    }
                }
            }
        } 

        $strLinkAction = rtrim($strLinkAction,' | ');
        return $strLinkAction != '' ? $strLinkAction   : '---';
    }

    public function adminLanguageContentSet($code){
        if($code){
            $_SESSION['adminLanguageContent'] = $code;
        } 
    }

    public function adminLanguageContentGet(){
        return $_SESSION['adminLanguageContent'] ? $_SESSION['adminLanguageContent'] : 'vi_VN';
    }
    //--End check add language

    //--Category
    public function getCategory() {
        $categoryModel = $this->getServiceLocator()->get('Category\Admin\Model\Category');
        $items = $categoryModel->getAllItemsToKeyVal(array(), array('key' => 'id', 'value' => 'title'));
        
        return $items;
    }

    public function getCatTreeOptions($lang = ''){
        $parentOptions = $this->getServiceLocator()->get('Category\Admin\Model\Category')->getTreeOptions($lang);
        return $parentOptions;
    }
    //--End category


    //--Content type

    public function getContentType($group = '', $isPublic = false) {
        $model = $this->getServiceLocator()->get('Contenttype\Admin\Model\Contenttype');

        $arrWhere = array();
        if ($group) {
            $arrWhere['group'] = $group;
        }

        $arrWhere['status'] = $isPublic ? 1 : '';
        $items = $model->getAllItemsToKeyVal($arrWhere, array('key' => 'type', 'value' => 'title'),'ordering asc');

        return $items;
    }

    public function getContentTypeName($type = '') {
        $model = $this->getServiceLocator()->get('Contenttype\Admin\Model\Contenttype');
        $items = $model->getItem(array('type' => $type));

        return $items;
    }

    public function getContentTypeByType($type = '') {
        $model = $this->getServiceLocator()->get('Contenttype\Admin\Model\Contenttype');
        $items = $model->getItem(array('type' => $type));

        return $items;
    }

    public function contentTypeGroup($key = '') {
        $arr = array();
        $arr['retail'] = array('id' => 'retail', 'title' => 'Retail');
        $arr['residential'] = array('id' => 'residential', 'title' => 'Residential');
        $arr['fitness'] = array('id' => 'fitness', 'title' => 'Fitness');
        $arr['static_content'] = array('id' => 'static_content', 'title' => 'Static content');
        
        if ($key) {
            return $arr[$key];
        }

        return $arr;
    }

    public function contentTypeGroupKeyVal() {
        $arr = $this->contentTypeGroup();
        $arrFix = array();
        foreach ($arr as $value) {
            $arrFix[$value['id']] = $value['title'];
        }

        return $arrFix;
    }
    //--End Content type

    public function getPostByWheres($wheres = array(), $isGetkeyVal=false) {
       $model = $this->getServiceLocator()->get('Content\Admin\Model\Post');
        if($isGetkeyVal){
            $items = $model->getAllItemsToKeyVal($wheres, array('key' => 'id', 'value' => 'title'));
        }else{
            $items = $model->getAllItemsToArray($wheres);
        }
        return $items;  
    }

    public function getProvinces($wheres = array(), $isGetkeyVal=false) {
       $model = $this->getServiceLocator()->get('Content\Admin\Model\Province');
        if($isGetkeyVal){
            $items = $model->getAllItemsToKeyVal($wheres, array('key' => 'id', 'value' => 'title'));
        }else{
            $items = $model->getAllItemsToArray($wheres);
        }
        return $items;  
    }

    public function getUser($id = '') {
        $model = $this->getServiceLocator()->get('User\Front\Model\User');
        $items = $model->getItemById($id);
        
        return $items;  
    }

    public function residentialOverviewInfoType($key = '') {
        $arr = array();
        $arr['overview'] = array('id' => 'overview', 'title' => 'Overview');
        $arr['introduction'] = array('id' => 'introduction', 'title' => 'Introduction');
        $arr['concept'] = array('id' => 'concept', 'title' => 'Concept');
        $arr['sale_policy'] = array('id' => 'sale_policy', 'title' => 'Sale policy');
        
        if ($key) {
            return $arr[$key];
        }

        return $arr;
    }

    public function residentialOverviewInfoTypeKeyVal() {
        $arr = $this->residentialOverviewInfoType();
        $arrFix = array();
        foreach ($arr as $value) {
            $arrFix[$value['id']] = $value['title'];
        }

        return $arrFix;
    }

    public function bannerPageType($key = '') {
        $arr = array();
        $arr['retail_brand'] = array('id' => 'retail_brand', 'title' => 'Retail: brand');
        if ($key) {
            return $arr[$key];
        }

        return $arr;
    }

    public function bannerPageTypeKeyVal() {
        $arr = $this->bannerPageType();
        $arrFix = array();
        foreach ($arr as $value) {
            $arrFix[$value['id']] = $value['title'];
        }

        return $arrFix;
    }

    public function holineType($key = '') {
        $arr = array();
        $arr['retail'] = array('id' => 'retail', 'title' => 'Retail');
        $arr['residential'] = array('id' => 'residential', 'title' => 'Residential');
        $arr['fitness'] = array('id' => 'fitness', 'title' => 'Fitness');
        if ($key) {
            return $arr[$key];
        }

        return $arr;
    }

    public function holineTypeKeyVal() {
        $arr = $this->holineType();
        $arrFix = array();
        foreach ($arr as $value) {
            $arrFix[$value['id']] = $value['title'];
        }

        return $arrFix;
    }

    /*--****************** END ADMIN ****************---*/

    /*--****************** FRONT END*****************---*/
    public function getPostByOptions($type = 'news', $limit = 0, $language='*', $wheres = array()) {
        $model = $this->getServiceLocator()->get('Content\Front\Model\Post');
        $items = $model->getByOptions($type, $limit, $language, $wheres);
        
        return $items;  
    }

    public function getPostItem($wheres = array()) {
        $model = $this->getServiceLocator()->get('Content\Front\Model\Post');
        $item = $model->getItem($wheres);
        return $item;  
    }

    public function getSettingValByKey($key= '') {
        $settingService = $this->getServiceLocator()->get('SettingService');
        return $settingService->getByIdentity($key);
    }

    //*-- Check close compaign------*/
    public function isCloseCammpaign(){
        $arrClose = array('status'=>false,'message'=>'Chương trình vẫn đang còn chạy');
        $dateCloseCampaign = trim($this->getSettingValByKey('core.date_close_campaign'));
        $dateCloseCampaign = $dateCloseCampaign ? $dateCloseCampaign : '2020-01-01'; //Y-m-d

        $curDate = date('Y-m-d');
        if(strtotime($curDate) > strtotime($dateCloseCampaign)){
            $arrClose = array('status'=>true,'message'=>'Thời gian tham gia chương trình đã hết, cảm ơn bạn đã quan tâm.');
        }

        return $arrClose;
    }

    /*-- Check site on live --*/
    public function isOnLive(){
        $serverName = $_SERVER['SERVER_NAME'];
        if(defined("LIVE_HOST_NAME")){
            $serverNameConfig = trim(LIVE_HOST_NAME);
            if($serverName == $serverNameConfig || $serverName == 'www.'.$serverNameConfig){
                return true;
            }
        }

        return false;   
    }

    public function getLanguageList($wheres = array(), $isGetkeyVal = false) {
        if(!isset($wheres['status'])){
            $wheres['status'] = 1;
        }

        $langModel = $this->getServiceLocator()->get('Language\Admin\Model\Language');
        if($isGetkeyVal){
            $items = $langModel->getAllItemsToKeyVal($wheres, array('key' => 'lang_code', 'value' => 'title'));
        }else{
            $items = $langModel->getByOptions(0, $wheres);
        }
        return $items;    
    }

    public function floorplanGetBrands($floors){
        $floorsFix = array();
        $postModel = $this->getServiceLocator()->get('Content\Front\Model\Post');
        if($floors){
            foreach ($floors as $kF => $vF) {
                $brandList = $postModel->getByMultiCate('retail_brandlist',0, $curlang, $vF['id']);

                 if($brandList){
                    $categoryListFix = array();
                    $cateFix = 0;
                    foreach ($brandList as $key => $value) {
                        if($key == 0){
                            $cateFix = $value['category'];
                            $categoryListFix[$cateFix][] = $value;
                        }else{
                            if($value['category'] == $cateFix){
                                $categoryListFix[$cateFix][] = $value;
                            }else{
                                $cateFix = $value['category'];
                                $categoryListFix[$cateFix][] = $value;
                            }
                        }
                    }

                    $vF['brandCateList'] = $categoryListFix;
                }

                $floorsFix[] = $vF;
            }
        }

        return $floorsFix;
    }
    /*--****************** END FRONT END*****************---*/

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
