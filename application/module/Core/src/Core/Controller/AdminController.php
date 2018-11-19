<?php

namespace Core\Controller;

use Core\Controller\CoreController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class AdminController extends CoreController {

    public $routerName = '';
    protected $factory;

    public function getFactory() {
        if (!$this->factory) {
            $this->factory = $this->getServiceLocator()->get('ServiceFactory');
        }
        return $this->factory;
    }

    public function addAction() {

        $form = $this->getForm();
        $request = $this->getRequest();
        $params = $this->getParams();
        if($params['lang_group']){
            $_SESSION['lang_group_add'] = $params['lang_group'];
        }else{
            unset($_SESSION['lang_group_add']);
        }
        
        if ($this->isValidForm($form)) {
            $data = $form->getData();
            $data = $this->getEventManager()->prepareArgs($data);
            $this->getEventManager()->trigger('onBeforeCreate', $this, $data);

            //TODO: Language
            if($this->isCtlAllowLangGroup($params)){
                $factory = $this->getFactory();
                $curLang = $factory->adminLanguageContentGet();
                $data['language'] = $data['language'] ? $data['language'] : $curLang;
                $data['lang_group'] = $_SESSION['lang_group_add'] ? $_SESSION['lang_group_add'] : $this->createdLangGroup();
            }

            unset($data['csrf']); //Remove csrf
            $return = $this->getModel()->save($data);

            if ($return) {
                $data['id'] = $return['item']['id'];
                $this->getEventManager()->trigger('onCreate', $this, $data);
                $this->addMessage('Save success');
                return $this->navigate($params, $return['id']);
            } else {
                $this->addMessage($return['message']);
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = $this->params()->fromRoute('id', 0);
        $form = $this->getForm();
        $params = $this->getParams();
        if ($this->isValidForm($form)) {
            $data = $form->getData();
            $data = $this->getEventManager()->prepareArgs($data);
            $this->getEventManager()->trigger('onBeforeEdit', $this, $data);
   
            //TODO: Language
            if($this->isCtlAllowLangGroup($params)){
                if(!$data['language']){
                    $factory = $this->getFactory();
                    $curLang = $factory->adminLanguageContentGet();
                    $data['language'] = $curLang;
                }
                
                if(!$data['lang_group']){ 
                    $data['lang_group'] = $this->createdLangGroup(); 
                }
            }

            unset($data['csrf']); //Remove csrf
            $return = $this->getModel()->save($data,$id);

            $this->getEventManager()->trigger('onEdit', $this, $data);
            if ($return) {
                $this->addMessage('Save success');
                return $this->navigate($params, $return['id']);
            } else {
                $this->addMessage($return['message']);
            }
        }
        $item = $this->getItem();
        $form->bind($item);
        return array('form' => $form, 'id' => $id, 'data' => $item);
    }

    /*Language*/
    private function isCtlAllowLangGroup($params = array()){
        $arrAllow = array('Content\Admin\Controller\Post','Category\Admin\Controller\Category');
        if(in_array($params['controller'], $arrAllow)){
            return true;
        }

        return false;
    }

    private function createdLangGroup($length = 10){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'.time();
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    /*End Language*/

    public function indexAction() {
        $model = $this->getModel();
        $params = $this->getParams();
        $model->setParams($params);
        $callAction = @$params['callaction'];
        $this->getEventManager()->trigger('onBeforeListing', $this, $params);
        if (trim($callAction)) {
            if (method_exists($this, $callAction)) {
                $return = call_user_func(array($callAction, $this));

                switch ($callAction) {
                    case 'deleteitem':
                        $return = $this->deleteItem();
                        break;
                    case 'publish':
                        $return = $this->publish();
                        break;
                    case 'unpublish':
                        $return = $this->unpublish();
                        break;  
                    
                    default:
                        # code...
                        break;
                }

                if (is_array($return) && $return['message']) {
                    $this->addMessage($return['message']);
                }
            } elseif (method_exists($model, $callAction)) {
                $return = call_user_func(array($callAction, $model));
                if (is_array($return) && $return['message']) {
                    $this->addMessage($return['message']);
                }
            } else {
                throw new \Exception('Method Not Exists!');
            }
        }
        $items = $model->getItems();
        if ($items && $items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }
        $items = $this->getEventManager()->prepareArgs($items);
        $this->getEventManager()->trigger('onListing', $this, $items);
        return new ViewModel(array(
            'items' => $items,
            'paging' => $model->getPaging(),
            'state' => $model->getStateObject()
        ));
    }

    public function publishAction() {
        $this->publish();
    }

    public function unpublishAction() {
        $this->unpublish();
    }

    public function deleteitemAction() {
        $params = $this->getParams();
        $data = array();
        $id = $params['id'];
        if ($id) {
            $data['data']['Choose'][] = $id;
            $this->getModel()->setParams($data);
            $this->getEventManager()->trigger('onBeforeDelete', $this, $params);
            $return = $this->getModel()->deleteItem();
            if ($return['message']) {
                $this->addMessage($return['message']);
            }

            $strType = $params['type'] ? '?type='.$params['type'] : '';
            return $this->redirectToUrl(BASE_URL . '/admin/' . $this->getRouteString() . '/index'.$strType);
        } else {
            $this->deleteItem();
        }
    }

    protected function deleteItem() {
        $params =  $this->getParams();
        $this->getModel()->setParams($params);
        $this->getEventManager()->trigger('onBeforeDelete', $this, $params);
        $return = $this->getModel()->deleteItem();
        if ($return['message']) {
            $this->addMessage($return['message']);
        }
    }

    protected function publish() {
        $params = $this->getParams();
        $params = $this->getEventManager()->prepareArgs($params);

        $this->getModel()->setParams($params);
        $this->getEventManager()->trigger('onBeforePublish', $this, $params);
        $this->getModel()->publish();
        $this->getEventManager()->trigger('onAfterPublish', $this, $params);

        return true;
    }

    protected function unpublish() {
        $params = $this->getParams();
        $params = $this->getEventManager()->prepareArgs($params);

        $this->getModel()->setParams($params);
        $this->getEventManager()->trigger('onBeforeUnPublish', $this, $params);
        $this->getModel()->unpublish();
        $this->getEventManager()->trigger('onAfterUnPublish', $this, $params);

        return true;
    }

    protected function getRouteString() {
        if ($this->routerName) {
            return $this->routerName;
        }
        $name = get_class($this);
        $names = explode("\\", $name);
        if (is_array($names)) {
            $name = $names[count($names) - 1];
            $ip = strpos($name, 'Controller');
            if ($ip) {
                $name = strtolower($name);
                $this->routerName = substr($name, 0, $ip);
            }
        }

        return $this->routerName;
    }

   protected function navigate($params = array(), $id='') {
        $typeFix = $params['type'];
        switch ($params['callaction']) {
            case 'save':
                $type = $typeFix ? '?type=' . $typeFix : '';
                return $this->redirectToUrl(BASE_URL . '/admin/' . $this->getRouteString() . $type);
                break;
            case 'save2new':
                $type = $typeFix ? '?type=' . $typeFix : '';
                return $this->redirectToUrl(BASE_URL . '/admin/' . $this->getRouteString() . '/add/' . $type);
                break;
            default:
                $typeFix = $typeFix ? '?type=' . $typeFix : '';
                return $this->redirectToUrl(BASE_URL . '/admin/' . $this->getRouteString() . '/edit/' . $id . $typeFix);
                break;
        }
    }

    protected function getItem() {
        $id = $this->params()->fromRoute('id', 0);
        $this->getEventManager()->trigger('onBeforeGetItem', $this);
        $item = $this->getModel()->getItemById($id);
        
        $item = $this->prepareArgs($item);
        $this->getEventManager()->trigger('onGetItem', $this, $item);
        
        return $item;
    }

    //----************* Process Upload image admin *****************---

    public function processDeleteImage($inputName = '', $params = array()){
        $ids = $params['data']['Choose'];
        if($ids){
            $model = $this->getModel();
            $folderSave = WEB_ROOT . '/media/images/';
            $folderSaveThumb = WEB_ROOT . '/media/images/thumb/';
            foreach ($ids as $id) {
                $item = $model->getItemById($id);
                if($item){
                    if (is_array($inputName)) {
                        foreach ($inputName as $value) {
                            $filename = $item[$value];
                            unlink($folderSave.$filename);
                            unlink($folderSaveThumb.$filename);
                        }
                    } else {
                        $filename = $item[$inputName];
                        unlink($folderSave.$filename);
                        unlink($folderSaveThumb.$filename);
                    }
                }
            }
        }
    }

    public function processCropImage($inputName = '', $params=array()) {
        $filename = $params[$inputName];
        if (empty($filename)) {
            $this->unsetParamsCrop($inputName, $params);
        } else {
            $x = $params['x_' . $inputName];
            $y = $params['y_' . $inputName];
            $w = $params['w_' . $inputName];
            $h = $params['h_' . $inputName];
            if($filename){
                $folderSave = WEB_ROOT . '/media/images/' . $filename;

                $isDeleteImg = $params['delete_'.$inputName];
                if($isDeleteImg){ //Is delete image
                    $folderSaveThumb = WEB_ROOT . '/media/images/thumb/' . $filename;
                    unlink($folderSave);
                    unlink($folderSaveThumb);
                    $params[$inputName] = '';
                }else{
                    $tmpFileWebRoot = WEB_ROOT . '/media/tmp/' . $filename;
                    if ($x >= 0 && $y >= 0 && $w > 0 && $h > 0) {
                        $this->crop($filename, $filename, array($x, $y, $w, $h), true, 'images');
                        if (!file_exists($folderSave)) {
                            @copy($tmpFileWebRoot, $folderSave);
                        }
                        $params[$inputName] = $filename;
                    } else { //No crop
                        @copy($tmpFileWebRoot, $folderSave);
                    }
                    unlink($tmpFileWebRoot);
                }
            }
            $this->unsetParamsCrop($inputName, $params);
            $this->thumbImage($filename); //Thumb Image        
        }
    }

    public function unsetParamsCrop($inputName = '', $params = array()) {
        if (is_array($inputName)) {
            foreach ($inputName as $value) {
                unset($params['x_' . $value]);
                unset($params['y_' . $value]);
                unset($params['w_' . $value]);
                unset($params['h_' . $value]);
                unset($params['delete_' . $value]);
            }
        } else {
            unset($params['x_' . $inputName]);
            unset($params['y_' . $inputName]);
            unset($params['w_' . $inputName]);
            unset($params['h_' . $inputName]);
            unset($params['delete_' . $inputName]);
        }
    }

    public function processMultiImage($params = array(), $multiImageName= 'multi_image') {
        $imagesStr = $params[$multiImageName];
        $multiCaption = $params[$multiImageName . '_caption'];
        if ($imagesStr) {
            $imagesSave = array();
            $images = explode(',', trim($imagesStr));
            foreach ($images as $key => $value) {
                $tmpFileWebRoot = WEB_ROOT . '/media/tmp/' . $value;
                $folderSave = WEB_ROOT . '/media/images/' . $value;
                $folderSaveThumb = WEB_ROOT . '/media/images/thumb/' . $value;
                if (!file_exists($folderSave)) {
                    @copy($tmpFileWebRoot, $folderSave);
                    $this->thumbImage($value, '414x414', $tmpFileWebRoot, $folderSaveThumb);
                    unlink($tmpFileWebRoot);
                }
                $caption = $multiCaption ? $multiCaption[$key] : '';

                $imagesSave[] = array('file' => $value, 'caption' => $caption);
            }
            $params[$multiImageName] = json_encode($imagesSave);
        }
        unset($params[$multiImageName . '_caption']);
    }
    //--End image, large image, multi image

    public function processUploadFile($params=array(), $type='files', $inputName = 'files') {
        $filename = $params[$inputName];
        //print_r($params); die;
        if($filename){
            $tmpFileWebRoot = WEB_ROOT . '/media/tmp/' . $filename;
            $folderSave = WEB_ROOT . '/media/'.$type.'/' . $filename;
            if (!file_exists($folderSave)) {
                @copy($tmpFileWebRoot, $folderSave);
                unlink($tmpFileWebRoot);
            }
            $params[$inputName] = $filename;
        }
    }


    public function processMultiCate($params = array(), $field= 'category_multi', $actionType='save'){
        $fieldValArr = $params[$field];
        if ($fieldValArr) {
            $params[$field] = $actionType == 'save' ? json_encode($fieldValArr) : json_decode($fieldValArr, true);
        }
    }
    //----************* END Process Upload image admin *****************---

}
