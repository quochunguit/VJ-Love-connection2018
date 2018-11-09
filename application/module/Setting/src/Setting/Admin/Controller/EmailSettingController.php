<?php

namespace Setting\Admin\Controller;

use Core\Controller\AdminController;
use Zend\Json\Json;

class EmailSettingController extends AdminController {

    public $routerName = 'emailsetting';

    public function indexAction() {

        $emailPath = $this->getBasePath();
        $items = $this->getListFile($emailPath);

        return array('items' => $items);
    }
    
    public function filemanAction(){
        $action = $_REQUEST['a'];
        
        $callAction = '';
        if($action){
            $callAction = $action.'Action'; 
        }
        if (method_exists($this, $callAction)) {
            
                //$data = call_user_func(array($callAction, $this));
                $data = $this->{$callAction}();
               
                return $this->getResponse()->setContent(Json::encode($data));
        }
        $data = array('status'=>'false','message'=>'Action Not Found');
        return $this->getResponse()->setContent(Json::encode($data));
                
    }

    private function getBasePath() {
        $emailPath = WEB_ROOT . DIRECTORY_SEPARATOR . 'application/templates/front/email';
        return $emailPath;
    }

    public function viewAction() {
        $file = $this->getBasePath() . $_REQUEST['p'];
        $code = highlight_file($file, true);
        return response(true, $code);
    }

    public function editfileAction() {
        $file = $this->getBasePath() . $_REQUEST['p'];
       
        $splOb = new \SplFileObject($file, 'r');
        ob_start();
        $splOb->fpassthru();
        $code = ob_get_clean();
        
        return array('message'=>$code, 'status'=>'true');
    }

    public function savefileAction() {
        $file = $this->getBasePath() . $_REQUEST['p'];
        $splOb = new \SplFileObject($file, 'w');
        $data = $_REQUEST['code'];
        $splOb->fwrite($data);
        return array('message'=>'Save successful', 'status'=>'true');
    }

    private function getListFile($path) {

        if (is_dir($path) || is_file($path)) {
            return new \DirectoryIterator($path);
        }
    }

}
