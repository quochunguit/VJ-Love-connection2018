<?php

namespace User\Admin\Controller;

use Core\Auth\Adapter\Db;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;
use Core\Plugin\Phpcaptcha;

class UserController extends AdminController {

    protected $userModel;
    protected $loginLogModel;
    public $routerName = 'user';

    public function __construct() {
        $this->modelServiceName = 'User\Admin\Model\User';
    }

    public function getLoginLogModel() {
        if (!$this->loginLogModel) {
            $model = $this->getServiceLocator()->get('User\Admin\Model\UserLoginLog');
            $this->loginLogModel = $model;
        }

        return $this->loginLogModel;
    }

    public function getForm() {
        $form = $this->getServiceLocator()->get('FormElementManager')->get('User\Admin\Form\UserForm');
        if ($this->getRequest()->isGet() && !$this->params()->fromRoute('id')) {

            $form->get('password')->setAttribute('class', 'required');
            $form->get('cfpassword')->setAttribute('class', 'required');
        }
        return parent::setupForm($form);
    }

    public function getLoginForm() {
        $form = $this->getServiceLocator()->get('FormElementManager')->get('User\Admin\Form\LoginForm');
        return parent::setupForm($form);
    }

    public function getItem() {
        $item = parent::getItem();
        unset($item['password']);
        return $item;
    }
     public function exportAction(){
        $params = $this->getParams();
        $type = $params['type'];
        
            $this->processExport();  /*Export with no image*/
    }

    private function processExport(){
        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $model = $this->getModel();
        $data = $model->getDataExport();

        require_once VENDOR_INCLUDE_DIR . '/phpoffice/phpexcel-1.8.1/Classes/PHPExcel.php';

        //Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

        //Set document properties
        $objPHPExcel->getProperties()->setCreator("BzCMS")
        ->setLastModifiedBy("BzCMS")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription('BzCMS')
        ->setKeywords("office 2007 openxml php")
        ->setCategory('BzCMS');

        //---Set name--   
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($style);     
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "Danh Sách User ");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
        $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold(true)->setSize(15); //Size of Intro file
        $objPHPExcel->getActiveSheet()->getStyle("A2:H2")->getFont()->setBold(true)->setSize(14); //Set size Head Title
        //--End set name--

        //Add some data
        $indexCellTitle = 2;
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$indexCellTitle, 'STT')
        ->setCellValue('B'.$indexCellTitle, 'Id')
        ->setCellValue('C'.$indexCellTitle, 'Họ và tên')
        ->setCellValue('D'.$indexCellTitle, 'Email')
        ->setCellValue('E'.$indexCellTitle, 'Điện thoại')
        ->setCellValue('F'.$indexCellTitle, 'Status')
        ->setCellValue('G'.$indexCellTitle, 'Created');
        
        $cell = 0;
        foreach($data as $key => $val){
            $user = $factory->getUser($val['user_id']);
            $cell = 3 + $key;
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $cell, $key + 1)
            ->setCellValue('B' . $cell, $val['id'])
            ->setCellValue('C' . $cell, $val['name'])
            ->setCellValue('D' . $cell, $val['email'])
            ->setCellValue('E' . $cell, $val['phone'])
            ->setCellValue('F' . $cell, $val['status'] == 1 ? 'Publish' : ($val['status'] == 2 ? 'Rejected' : 'Unpublish'))
                ->setCellValue('G' . $cell, $val['created']);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
          
            $objPHPExcel->getActiveSheet()->getStyle('I'. $cell)->getAlignment()->setWrapText(true);
        }
        $objPHPExcel->getActiveSheet()->getStyle("A2:K2".$cell)->getFont()->setSize(13); //Set size content

        //--Set border---
        // $style = array(
        //     'borders' => array(
        //         'allborders' => array(
        //             'style' => \PHPExcel_Style_Border::BORDER_THIN
        //         )
        //     )
        // );
        // $objPHPExcel->getActiveSheet()->getStyle("A2:K2".$cell)->applyFromArray($style);
        //--End Set border---

        //Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Users');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //--Set protected--
        //$objSheet = $objPHPExcel->getActiveSheet();
        //$objSheet->getProtection()->setSheet(true)->setPassword('contest');
        //--End Set protected--

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Users_' .time(). '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //--BLOCK ADMIN LOGIN----

    //-----Process action--------
    public function onBeforeListing(Event $e) {
        $params = $e->getParams();
        if ($params) {
            $model = $this->getModel();

            if (!$params['page']) {
                //Reset filter
                //End Reset filter
            }

            $group = $params['group'];
            if ($group) {
                $model->setState('filter.group', $group);
            }
        }
    }
    
    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();
    }

    //-----End Process action-------

    public function loginAction() {
        if (defined("ADMIN_SECURE") && defined("ADMIN_SECURE_COOKIE_CODE")) { //check secure admin
            if ($_COOKIE[ADMIN_SECURE] != ADMIN_SECURE_COOKIE_CODE) {
                return $this->redirectToRoute('home');
            }
        }

        if ($this->getServiceLocator()->get('Site')->getUser()->id) {
            return $this->redirectToUrl(BASE_URL . '/admin');
        }

        if ($this->isAjax()) {
            $data = $this->getParams();
            $validateResult = $this->loginValidate($data);
            if ($validateResult['status']) {
                $auth = $this->getServiceLocator()->get('AdminAuthService');
                $siteAuthAdapter = new Db($this->getModel());
                $siteAuthAdapter->setCredential(array('email' => $data['email'], 'password' => $data['password'], 'role' => '11'));
                $result = $auth->authenticate($siteAuthAdapter);
                if ($result->isValid()) {
                    $this->saveLoginLog(true);  //TODO: reset show captcha      
                    echo json_encode(array('status' => true, 'message' => 'Login is successful!', 'url_redirect' => BASE_URL . '/admin'));
                    exit();
                } else {
                    if (!$this->isStartBlockLogin()) {
                        $this->saveLoginLog();  //--Save login logs 
                    }

                    $message = $result->getMessages();
                    echo json_encode(array('status' => false, 'message' => $message));
                    exit();
                }
            } else {
                echo json_encode($validateResult);
                exit();
            }
        }
    }

    private function isBlockLogin() {

        if ($this->isStartBlockLogin()) {
            $seconds = (ADMIN_BLOCK_MINUTES * 60); // minutes

            $loginLogModel = $this->getLoginLogModel();
            $ip = $this->getIpClient();
            $loginLasted = $loginLogModel->getLoginFail($ip, date('Y-m-d H'), 'fail', 1);
            if ($loginLasted) {
                $curDateTime = date('Y-m-d H:i:s');
                $timeBlock = (time() - strtotime($loginLasted['created']));
                if ($timeBlock < $seconds) {
                    $timeLeft = $seconds - $timeBlock;
                    return array('status'=>true, 'timeLeft'=>gmdate("H:i:s", $timeLeft ));
                } else {
                    $this->saveLoginLog(true, true);
                }
            }
        }

       return array('status'=>false);
    }

    private function isStartBlockLogin() {
        $numFailBlock = ADMIN_LOGIN_FAIL;
        $countLoginFail = $this->countLoginFailPerHour();
        if ($countLoginFail >= $numFailBlock) {
            return true;
        }

        return false;
    }

    private function countLoginFailPerHour() {
        $loginLogModel = $this->getLoginLogModel();
        $ip = $this->getIpClient();
        $loginFailPerHour = $loginLogModel->getLoginFail($ip);
        return count($loginFailPerHour);
    }

    private function saveLoginLog($isUpdate = false, $isResetFail = false) {
        $loginLogModel = $this->getLoginLogModel();
        if (!$isUpdate) { //Add new
            $loginLogModel->save(array('title' => 'Login fail!', 'ip' => $this->getIpClient(), 'created' => date('Y-m-d H:i:s'), 'type' => 'fail', 'status' => 1));
        } else { //Update to reset check
            $loginLogModel->update(array('status' => 0), array('ip' => $this->getIpClient(), 'type' => 'fail', 'status' => 1)); //Reset fail
            if (!$isResetFail) {
                $loginLogModel->save(array('title' => 'Login success!', 'ip' => $this->getIpClient(), 'created' => date('Y-m-d H:i:s'), 'type' => 'success', 'status' => 1)); //Save success
            }
        }
    }

    private function loginValidate($params=array()) {
        if ($params != null) {
            $email = trim($params['email']);
            if ($email == '' || $email == 'Email') {
                return array('status' => false, 'element' => 'email', 'message' => 'Please enter email!');
            }

            $password = $params['password'];
            if ($password == '' || $password == 'Password') {
                return array('status' => false, 'element' => 'password', 'message' => 'Please enter password!');
            }

            $blogLogin = $this->isBlockLogin();
            if ($blogLogin['status']) {
                return array('status' => false, 'message' => 'Please try again after: ' . $blogLogin['timeLeft']. ' (H:m:s)');
            }
        } else {
            return array('status' => false, 'message' => 'Data is invalid!');
        }
        return array('status' => true, 'message' => 'Data is valid!');
    }

    //--End BLOCK ADMIN LOGIN----

    public function logoutAction() {
        $authService = $this->getServiceLocator()->get('AdminAuthService');
        $authService->clearIdentity();
        return $this->redirectToUrl(BASE_URL . '/admin/user/login');
    }

    /**
     * edit account, only login user can access this page and edit your account
     */
    public function accountAction() {
        $form = $this->getServiceLocator()->get('FormElementManager')->get('User\Admin\Form\UserForm');
        $form = parent::setupForm($form);

        if ($this->isValidForm($form)) {
            $data = $form->getData();
            $data = $this->getEventManager()->prepareArgs($data);
            $this->getEventManager()->trigger('onBeforeEdit', $this, $data);

            unset($data['csrf']);
            $return = $this->getModel()->save($data);

            $this->getEventManager()->trigger('onEdit', $this, $data);
            if ($return) {
                $this->addMessage('Save success');
            } else {
                $this->addMessage($return['message']);
            }
        }
        $item = $this->getServiceLocator()->get('Site')->getUser();
        $form->bind($item);
        return array('form' => $form, 'data' => $item);
    }

}
