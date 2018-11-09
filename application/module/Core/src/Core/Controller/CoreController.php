<?php

namespace Core\Controller;

use Core\Plugin\PhpThumb;
use Core\Controller\EventController;
use Zend\Session\Container;

class CoreController extends EventController {

    public $model;
    public $modelServiceName;
    public $form;

    public function getModel() {
        if (!$this->model) {
            $sm = $this->getServiceLocator();
            $this->model = $sm->get($this->getModelServiceName());
        }
        return $this->model;
    }

    public function getModelServiceName() {
        return $this->modelServiceName;
    }

    public function setupForm($form = '') {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($request->getPost());
        }
        return $form;
    }

    protected function isValidForm($form = '') {
        $request = $this->getRequest();
        if ($request->isPost()) {
            return $form->isValid();
        }
        return false;
    }

    public function getForm() {
        return null;
    }

    public function getFormMessages() {
        $form = $this->getForm();
        $return = array();
        if ($form != null) {
            $messages = $form->getMessages();
            if (!empty($messages)) {
                foreach ($messages as $el => $message):
                    $msg = array_values($message);
                    foreach ($message as $rule => $msg):
                        array_push($return, $msg);
                    endforeach;
                endforeach;
            }
        }
        return $return;
    }

    public function getViewModel() {
        return new ViewModel();
    }

    public function getParams($typeGet = 'all') {
        switch ($typeGet) {
            case 'json':
                $params = \Zend\Json\Json::decode($this->getRequest()->getContent(), true);
                break;
            case 'query':
                $params = $this->params()->fromQuery();
                break;
            case 'post':
                $params = $this->params()->fromPost();
                break;
            case 'route':
                $params = $this->params()->fromRoute();
                break;  
            default:
                $query = $this->params()->fromQuery();
                $post = $this->params()->fromPost();
                $route = $this->params()->fromRoute();
                $params = array_merge($query, $post, $route);
                break;
        }
       
        return $params;
    }

    public function redirectToUrl($url = '') {
        return $this->redirect()->toUrl($url);
    }

    public function redirectToRoute($route = '', $params = array()) {
        return $this->redirect()->toRoute($route, $params);
    }

    public function addMessage($message = '', $type = '') {
        $this->flashMessenger()->addMessage($message);
    }

    public function setMetaData($data = array(), $postTitle = '') {
        if (!$data) {
            $data = array();
        }
        $headTitle = $this->getServiceLocator()->get('ViewHelperManager')->get('headTitle');
        $headMeta = $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta');

        $headTitle->setSeparator(' - ');

        if (array_key_exists('description', $data) && !empty($data['description'])) {
            $headMeta()->appendName('description', $data['description']);
        } else {
            //get global description
            $sitedesc = $this->setting('core.metadescription');
            $headMeta()->appendName('description', $sitedesc);
        }
        if (array_key_exists('keywords', $data) && !empty($data['keywords'])) {
            $headMeta()->appendName('keywords', $data['keywords']);
        } else {
            //get global keyword
            $sitekeyword = $this->setting('core.metakeyword');
            $headMeta()->appendName('keywords', $sitekeyword);
        }
        if (@empty($data['pagetitle'])) {
            $data['pagetitle'] = $postTitle;
        }
        if (array_key_exists('pagetitle', $data) && !empty($data['pagetitle'])) {
            $headTitle->append($data['pagetitle']);
        }

        $sitetitle = $this->setting('core.sitename');
        $this->layout()->siteMeta = array('title'=>$sitetitle,'description'=>$sitedesc);

        $headTitle->append($sitetitle);
    }


    //--Crop---
    public function doCropImage($data=array()) {
        include_once \VENDOR_INCLUDE_DIR . '/phpthumb/ThumbLib.inc.php';

        $x = $data['x_img_crop'];
        $y = $data['y_img_crop'];
        $w = $data['w_img_crop'];
        $h = $data['h_img_crop'];
        $imgName = $data['image_name'];
        $newImgName = 'crop_' . $imgName;

        $pathImgCrop = $data['path_img_crop'];
        $pathSaveImgCrop = $data['path_save_img_crop'];

        $thumb = \PhpThumbFactory::create($pathImgCrop . '/' . $imgName);
        $thumb->crop($x, $y, $w, $h);
        $thumb->save($pathSaveImgCrop . '/' . $newImgName);

        return array('status' => true, 'new_img_name' => $newImgName, 'message' => 'Cắt hình thành công!');
    }

    public function crop($source = '', $origin = '', $options=array(), $deleteSource = false, $folder = 'tmp') {
        include_once WEB_ROOT . '/upload/libs/cropimage.class.php';

        $folder_source = DS . 'media' . DS . 'tmp';
        $folder_origin = DS . 'media' . DS . $folder;

        $x = $options[0];
        $y = $options[1];
        $w = $options[2];
        $h = $options[3];

        $crop = new \Cropimage($x, $y, $w, $h);

        $crop->setPathToImgCrop(WEB_ROOT . $folder_source);
        $crop->setPathToImgAfterCrop(WEB_ROOT . $folder_origin);
        $crop->setImgFile($source);

        $crop->setNewImgFile($origin);

        $crop->crop();

        if ($deleteSource) {
            unlink($folder_source);
        }
    }
    //--End Crop---

    //--Thumb Image
    public function thumbImage($image = '', $size = '', $org = '', $des = '') {
        $size = $size ? $size : '300x300';
        if ($image) {
            $org = $org ? $org : WEB_ROOT . '/media/images/' . $image;
            $des = $des ? $des : WEB_ROOT . '/media/images/thumb/' . $image;
            if (file_exists($org) && !file_exists($des)) {
                $sizeArr = explode('x', trim($size));
                $width = $sizeArr[0];
                $height = $sizeArr[1];
                $thumb = new PhpThumb($org);
                $thumb->thumb($org, $des, $width, $height);
            }
        }
    }

    public function thumbImageCenter($image = '', $size = '', $org = '', $des = '') {
        $size = $size ? $size : '300x300';
        if ($image) {
            $org = $org ? $org : WEB_ROOT . '/media/images/' . $image;
            $des = $des ? $des : WEB_ROOT . '/media/images/thumb/' . $image;
            if (file_exists($org) && !file_exists($des)) {
                $thumb = new PhpThumb($org);
                $thumb->cropImageCenter($des, $size);
            }
        }
    }
    //--End Thumb Image

    //Check request is ajax
    public function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    //--Return ajax data
    public function returnJsonAjax($arr = array()) {
        echo json_encode($arr);
        exit();
    }
    
    //--Get ip server
    public function getIpClient() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    } 

    //--Return array 2 array nested
    public function fixListPerView($items=array(), $itemPerView = 3) {
        if ($items) {
            $listFixArr = array();
            $index = 0;
            foreach ($items as $value) {
                $index++;
                $itemArr[] = $value; // is_object($value) ? get_object_vars($value) : $value;
                if ($index % $itemPerView == 0 || ((count($items) - $index) < ($itemPerView - ($itemPerView - 1) ))) {
                    $listFixArr[] = $itemArr;
                    $itemArr = array();
                }
            }

            return $listFixArr;
        }
    }

    //Get helper
    public function getHelper($helperName = '') {
        if($helperName){
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
        }else{
            $helper = $this->getServiceLocator()->get('viewhelpermanager'); 
        }
        return $helper;
    }

    //writeFile    
    public function writeFile($pathPhysical = '', $fileName = '', $content = ''){
        $pathPhysical = $pathPhysical ? $pathPhysical : WEB_ROOT. DS."media";
        $myfile = @fopen($pathPhysical.DS.$fileName, "w") or die("Unable to open file!");
        @fwrite($myfile, $content);
        fclose($myfile);
    }

    //--Zip FILE---
    /* creates a compressed zip file */
    public function createZip($files = array(),$destination = '',$overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new \ZipArchive();
            if($zip->open($destination,$overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file,$file);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            
            //close the zip -- done!
            $zip->close();
            
            //check to make sure the file exists
            return file_exists($destination);
        }
        else
        {
            return false;
        }
    }

    //--Save image base64--
    public function saveImageBase64($imageBase64 = '', $outPath = '') {
        list($type, $imageBase64) = explode(';', $imageBase64);
        list(, $imageBase64) = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);

        file_put_contents($outPath, $imageBase64);
    }

    //--Get all src image from html string--
    public function getAllSrcImageFromHtmlStr($htmlStr = ''){
        if($htmlStr){
            preg_match_all('/<img(.*?)src=("|\'|)(.*?)("|\'| )(.*?)>/s', $htmlStr, $images);
        }
        return $images ? $images[3] : array();
    }

    //--User login--
    public function getUserLogin($field = '') {
        $sm = $this->getServiceLocator();
        $userService = $sm->get('User\Front\Service\UserService');
        $user = $userService->getCurrentUser();
        
        if($field){
            return $user[$field];
        }

        return $user;
    }

    public function getUserIdLogin() {
        $user = $this->getUserLogin();
        return $user['id'];
    }
    //--End User login--

    //*********FRONT-------------

    //--Set get lang code
    public function setLangCode($langCode = ''){
        $sm = $this->getServiceLocator();
        $translator= $sm->get('MvcTranslator');
        if($langCode){
            $sessionContainer = new Container('translate_locale');
            $sessionContainer->offsetSet('myLocale', $langCode);
            $translator->setLocale($langCode);
        }
    }

    public function getLangCode($isGetCodeShort = false){
        $sm = $this->getServiceLocator();
        $translator= $sm->get('MvcTranslator');

        if(!$isGetCodeShort){
            return $translator->getLocale();
        }else{
            $code =$translator->getLocale();
            $codeArr = explode('_', trim($code));
            return $codeArr[0];
        }
    }
    //--End set get lang code

    public function phoneFix($phone = '') {
        $phoneFix = $phone;
        $phoneLen = strlen($phone);
        $codeVN0 = substr($phone, 0, 1);
        $codeVN2 = substr($phone, 0, 2);

        if ($codeVN0 == '0') {
            $phoneFix = substr($phone, 1, $phoneLen);
        }
        if ($codeVN2 == '84') {
            $phoneFix = substr($phone, 2, $phoneLen);
        }
        return $phoneFix;
    }

    public function countryPhoneFix($phone = '', $country_phone_code){
        $phoneFix = $phone;
        $phoneLen = strlen($phone);
        $code1 = substr($phone, 0, 1);
        $code2 = substr($phone, 0, 2);
        $code3 = substr($phone, 0, 3);

        if ($code1 == '0') {
            $phoneFix = substr($phone, 1, $phoneLen);
        }
        if ($code2 == $country_phone_code) {
            $phoneFix = substr($phone, 2, $phoneLen);
        }
        if ($code3 == $country_phone_code) {
            $phoneFix = substr($phone, 3, $phoneLen);
        }
        return $phoneFix;
    }

    public function getUserAge($birthday = ''){
        $birthday = date('Y-m-d', strtotime(str_replace('/','-', $birthday)));
        $age = date_create($birthday)->diff(date_create('today'))->y;
        return $age;
    }

    public function randomArray($arr = array(), $numGet = 1){    
        if($arr){
            $indexRand = array_rand($arr, $numGet);
            if($numGet > 1){
                foreach ($indexRand as $key => $value) {
                   $resultRand[] = $arr[$value];
                }
            }else{
                $resultRand =  $arr[$indexRand]; 
            }
        }

        return $resultRand;
    }
    //*********END FRONT-------------

}
