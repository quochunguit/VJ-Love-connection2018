<?php

namespace User\Front\Controller;

use Core\Controller\FrontController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Core\Auth\Adapter\Db;
use App\HashUtil;
use Core\Plugin\Sms;

class UserController extends FrontController {

    const METHOD_POST_PARAM = '';

    //--******************** API **************************:

    public function loginAction(){
        $login = $this->getUserLogin();
        $logout = '';

        if($login){
            $logout = '<button id="site-logout" onclick="App.Site.userLogout('.$login->id.');">Logout</button>';
        }

        return new ViewModel(array(
            'login'=>$login,
            'logout'=>$logout
        ));
    }

    public function registerAction(){

    }

    //--Register--
    public function apiregisterAction(){
        $params = $this->getParams();
        $resultValid = $this->validateRegister($params);
        if($resultValid['status']){
            $curUser = $resultValid['curUser'];
            $userModel = $this->getUserModel();
            $phone_code = $params['phonecode'];
            $phone = $phone_code . $this->countryPhoneFix($params['phone'], $phone_code);
            $mobileCode = $this->randomCode(4);
            $data = array(
                //'token'=> HashUtil::createRandomKey(),
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => $params['password'],
                'phone' => $phone,
                'location'=> $params['location'],
                'mobile_code'=>$mobileCode,
                'ip' => $this->getIpClient(),
                'is_updated_info' => 1,
                'status' => 0
                );

            //--Process save info---
            if(!$curUser){ //User normal
                $data['social_type'] = 'Normal';
                $data['side'] = 'website';
            }else{ //User register by fb
                $userId = $curUser['id'];
            }
            //--End Process save info---

            $return = $userModel->register($data, $userId);
            if ($return['status']) {
                //login
                $return = $this->auth($data['password'],$data['email']);
                if($return['status_key']=='inactive'){
                   $result= $this->sendSMS($phone, $mobileCode); //Send SMS
                   if($result['status']){
                       $user = $userModel->getUser($return['id']);

                       $this->returnJsonAjax(array('status'=>true,'button'=>$this->translate('Continue'),'message'=>$this->translate('RegisterSuccess'), 'data'=>$this->apiProcessUser($user)));
                   }
                }



            }else{
                $this->returnJsonAjax(array('status'=>false,'message'=>'Đã xảy ra lỗi, vui lòng thử lại sau!'));
            }
        }else{
            $this->returnJsonAjax($resultValid);
        }

        $this->returnJsonAjax(array('status'=>false,'message'=>'Dữ liệu không hợp lệ!'));
    }

    private function validateRegister($params){
        if($params){
            $userModel = $this->getUserModel();

            $userToken = $params['user_token'];
            if($userToken){
                $curUser = $userModel->getUserByToken($userToken);
            }

            if($params['name'] == ''){
                return array('status'=>false,'message'=>'Vui lòng nhập họ và tên!');
            }

            $email = $params['email'];
            if($email == ''){
                return array('status'=>false, 'message'=>'Vui lòng nhập email!');
            }else{
                $validator = new \Zend\Validator\EmailAddress();
                if (!$validator->isValid($email)) {
                   return array('status'=>false, 'message'=>'Email không đúng định dạng!');
               } 

               if($curUser['email']!= $email){
                $isExist = $userModel->isEmailExists($email);
                if($isExist['status']){
                    return array('status'=>false,'element'=>'regemail', 'message'=>$this->translate('Email_exist'));
                }
            }
        }

        $phone = $params['phone'];
        $phone_code = $params['phonecode'];
        if($phone == ''){
            return array('status'=>false, 'message'=>'Vui lòng nhập số điện thoại!');
        }else{
            if (!is_numeric($phone)) {
                return array('status' => false, 'message' => 'Số điện thoại phải là một chuối số!');
            }

            $minLen = 5;
            if (strlen($phone) < $minLen && substr($phone, 0, 1) == '0') {
                return array('status' => false, 'message' => 'Số điện thoại phải ít nhất '.$minLen.' ký tự!');
            }else if(strlen($phone) < $minLen - 1){
                return array('status' => false, 'message' => 'Số điện thoại phải ít nhất '.$minLen.' ký tự!');
            }

            $maxLen = 13;
            if (strlen($phone) > $maxLen) {
                return array('status' => false, 'message' => 'Số điện thoại phải nhiều nhất '.$maxLen.' ký tự!');
            }

            if($curUser['phone'] != $phone){
                $check = $userModel->isExists('phone', $phone_code . $this->countryPhoneFix($phone, $phone_code));
                //$check = $userModel->isExists('phone', '0' . $this->phoneFix($phone));
                //$check1 = $userModel->isExists('phone', '84' . $this->phoneFix($phone));
                if ($check['status']) {
                    return array('status' => false, 'element' => 'regphone', 'message' => $this->translate('Phone_exist'));
                }
            }
        }

        /*if($params['address'] == ''){
            return array('status'=>false, 'message'=>'Vui lòng nhập địa chỉ!');
        }*/

        if(!$curUser){ //Register by fb then pass check step
            $pass = $params['password'];
            if($pass == ''){
                return array('status'=>false, 'message'=>'Vui lòng nhập mật khẩu!');
            }else{
                if($pass != $params['cfpassword']){
                    return array('status'=>false, 'message'=>'Nhập lại mật khẩu không khớp!');
                }
            }
        }
        }else{
           return array('status'=>false, 'message'=>'Dữ liệu không hợp lệ!');
       }
       return array('status'=>true, 'message'=>'Dữ liệu hợp lệ!','curUser'=>$curUser);
   }

public function apiverifysmsAction(){
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $curLang = $this->getLangCode(true);
    //$token = $params['user_token'];
    $mobileCode = $params['mobile_code'];

    $userModel = $this->getUserModel();
    //$user = $userModel->getUserByToken($token);
    $user = $this->getUserLogin();
    if($user){
        $userId = $user['id'];
        $resultValid = $this->checkUserStatus($user);
        if(!$resultValid['status'] && $resultValid['status_key'] == 'inactive'){
            if(strtolower(trim($mobileCode)) == strtolower(trim($user['mobile_code']))){
                $active = $userModel->active($userId);
                if($active['status']){

                        //-Token login--
                    $this->createdUserToken($userId, $userModel);
                        //-End Token login--

                    $user = $userModel->getUser($userId);
                    unset($_SESSION['need_active']);
                    $mail = $this->getServiceLocator()->get('SendMail');
                    if($curLang=='vi'){
                        $mail->send(array(
                            'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                            'to' => array('name' => $user['name'], 'email' => $user['email']),
                            'subject' => 'VIETJET - CHÀO MỪNG ĐẾN HÀNH TRÌNH  "KẾT NỐI YÊU THƯƠNG, YÊU LÀ PHẢI TỚI',
                            'template' => 'email/welcome',
                            'data' => array(
                                'user_name'=>$user['name'],

                            )
                        ));
                    }else{
                        $mail->send(array(
                            'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                            'to' => array('name' => $user['name'], 'email' => $user['email']),
                            'subject' => 'VIETJET -  WELCOME TO THE "LOVE CONNECTION - LOVE IS REAL TOUCH" CAMPAIGN OF VIETJET.',
                            'template' => 'email/welcome_en',
                            'data' => array(
                                'user_name'=>$user['name'],

                            )
                        ));
                    }

                    $this->returnJsonAjax(array('status'=>true,'message'=>$this->translate('Activationcodesuccess'),'data'=>$this->apiProcessUser($user)));
                }else{
                    $this->returnJsonAjax($active);
                }
            }else{
                $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Invalidactivationcode')));
            }
        }else{
            if($resultValid['status']){
                $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Accounthasactived')));
            }else{
                $this->returnJsonAjax($resultValid);
            }
        } 
    }else{
       $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Unableactive')));
   }
}

public function apiverifyresendsmsAction(){
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $phone = $params['phone'];
    $token = $params['user_token'];
    if($token){
        $userModel = $this->getUserModel();
        $user = $userModel->getUserByToken($token);
        if($user){
            $mobileCode = $user['mobile_code'];
                //--Validate--
            if(!$mobileCode && $user['status'] == 1){
               $this->returnJsonAjax(array('status' => false, 'message' => 'Tài khoản của bạn đã được kích hoạt!'));
           }

           if(!$mobileCode && $user['status'] == 0){
               $this->returnJsonAjax(array('status' => false, 'message' => 'Tài khoản của bạn đã được kích hoạt nhưng đã bị khóa!'));
           }

           if($phone){
            if (!is_numeric($phone)) {
               $this->returnJsonAjax(array('status' => false, 'message' => 'Số điện thoại phải là một chuối số!'));
           }

           $minLen = 5;
           if (strlen($phone) < $minLen ) {
               $this->returnJsonAjax(array('status' => false, 'message' => 'Số điện thoại phải ít nhất '.$minLen.' ký tự!'));
           }

           $maxLen = 13;
           if (strlen($phone) > $maxLen) {
               $this->returnJsonAjax(array('status' => false, 'message' => 'Số điện thoại phải nhiều nhất '.$maxLen.' ký tự!'));
           }

           $phoneFix0 = '0' . $this->phoneFix($phone);
           $phoneFix84 = '84' . $this->phoneFix($phone);
           if($user['phone'] != $phoneFix0 && $user['phone'] != $phoneFix84){
            $check = $userModel->isExists('phone', $phoneFix0);
            $check1 = $userModel->isExists('phone', $phoneFix84);
            if ($check['status'] || $check1['status']) {
               $this->returnJsonAjax(array('status' => false, 'el' => 'phone', 'message' => 'Số điện thoại này đã có người sử dụng để đăng ký trong hệ thống!'));
           }
       } 
   }
                //--End Validate--

                //--Resend sms---
   $limitResend = 2;
   if($user['verify_resend'] < $limitResend){
    $phone = $phone ? $phone : $user['phone'];
                    $isSend = $this->sendSMS($phone, $mobileCode); //Send SMS
                    if($isSend){
                        $userModel->increase(array('id' => $user['id']), 'verify_resend');
                        $this->returnJsonAjax(array('status'=>true, 'message'=>'Gửi lại tin nhắn thành công!'));
                    }  
                }else{
                   $this->returnJsonAjax(array('status'=>false, 'message'=>'Bạn chỉ được gửi lại tin nhắn tối đa '.$limitResend.' lần!')); 
               }
                //--End Resend sms---
           }else{
               $this->returnJsonAjax(array('status'=>false, 'message'=>'Bạn cần đăng ký tài khoản!')); 
           }
       }else{
           $this->returnJsonAjax(array('status'=>false, 'message'=>'Dữ liệu không hợp lệ!')); 
       }
   }

   private function sendSMS($phone, $message){
       $sms = new Sms();
       $return = $sms->sendSoap($phone,'Your activation code: '.$message);
       return $return;
}
    //--End register

    //--Login facebook
public function apiloginfbAction(){
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $accessToken = $params['access_token'];
    if ($params && $accessToken) {

        $config = array('appId' => FB_APP_ID,'secret' => FB_APP_SECRET,'cookie' => true);
            //giãn thời gian hết hạn của token lên 60 ngày.
        $extend_token_url = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $config['appId'] . "&client_secret=" . $config['secret'] . "&fb_exchange_token=" . $accessToken;
        $extend_response = file_get_contents($extend_token_url);

        $extend_params = null;
        $extend_params =json_decode($extend_response );
        //parse_str($extend_response, $extend_params);

        $accessTokenRefresh = $extend_params->access_token;

        $graph_url = "https://graph.facebook.com/me?fields=id,name,email&access_token=" .  $accessTokenRefresh;

        $user = json_decode(file_get_contents($graph_url));
        $user->token =  $accessTokenRefresh;
        //print_r($user);die;
        $return = array();
        if ($user->id) {
            $userModel = $this->getUserModel();
            $userExist = $userModel->getUserByFbUid($user->id);
            if ($userExist['social_id']) {
                $userLogin = $userExist;
            } else {
                $userLogin = $this->fbcomplete($user);
                //print_r($userLogin);die;
            }

                //--Check user valid--
            $resultValid =  $this->checkUserStatus($userLogin);

            if($resultValid['status'] || $resultValid['status_key'] == 'reg_fb' ){
                if($resultValid['status']){
                        //-Token login--
                    $token = $this->createdUserToken($userLogin['id'],$userModel);
                    if($token){
                        $userLogin['token'] = $token;
                    }
                        //-End Token login--
                }

                $userLoginFix = $this->apiProcessUser($userLogin);
                $auth = $this->getServiceLocator()->get('FrontAuthService');
                $siteAuthAdapter = new \Core\Auth\Adapter\Social($userModel, 'Facebook');
                $siteAuthAdapter->setCredential($userLogin);
                $result = $auth->authenticate($siteAuthAdapter);
                if($userLoginFix['phone']==''){
                    $this->returnJsonAjax(array('status'=>false,'phone'=>$userLoginFix['phone']));
                }elseif($userLoginFix['status']==0){
                    //echo 'aaaaaa';die;
                    $this->returnJsonAjax(array('status'=>false,'phone'=>$userLoginFix['phone'],'location'=>$userLoginFix['location'],'need_active'=>true));
                }else{
                    if($userLoginFix['status']==1){
                        $this->returnJsonAjax(array('status'=>true,'data'=>$userLoginFix));
                    }else{
                        $this->returnJsonAjax(array('status'=>false,'message'=>'Error, try again later!'));
                    }
                }

            }else{
                $this->returnJsonAjax($resultValid); 
            }
                //--End check user valid
        }else{
           $this->returnJsonAjax(array('status' => false, 'message' => 'Đăng nhập bằng Facebook thất bại!'));
       }
   }else{
    $this->returnJsonAjax(array('status' => false, 'message' => 'Dữ liệu không hợp lệ!')); 
}

$this->returnJsonAjax(array('status' => false, 'message' => 'Đã có lỗi xảy ra, vui lòng thử lại!'));
}

public function fbcomplete($data)
{
    $userinfo = array();
    if ($data) {
        $model = $this->getUserModel();

        $urlAvatarFB = 'http://graph.facebook.com/' . $data->id . '/picture?width=280&height=280';
        $email = $data->email;
        $userinfo['social_token'] = $data->token;
        $userinfo['social_id'] = $data->id;
            $userinfo['social_link'] = $data->link; //save link
            $userinfo['gender'] = $data->gender == 'male' ? 'Male' : 'Female'; //gender
            $userinfo['social_picture'] = $urlAvatarFB;
            $socialType = 'Facebook';

            $userByEmail = get_object_vars($model->getUserByEmail($email));
            if ($userByEmail) { //Exist email
                if($userByEmail['social_type']=='Google'){
                    $this->returnJsonAjax(array('status' => false, 'status_key'=>'alert', 'message' => $this->translate('Email_exist')));
                }
                //$userinfo['activation_code'] = ''; //Auto active
                //$userinfo['mobile_code'] = ''; //Auto active
                //$userinfo['status'] = 1; //Auto active

                $return = $model->save($userinfo, $userByEmail['id']);
            } else { //Not exist email
                $userinfo['social_type'] = $socialType;
                $userinfo['name'] = $data->name;
                $userinfo['first_name'] = $data->first_name;
                $userinfo['last_name'] = $data->last_name;
                $userinfo['avatar'] = $urlAvatarFB;

                $userinfo['email'] = $email;

                $userinfo['role'] = 10;
                $password = HashUtil::randomPassword();
                $userinfo['password'] = HashUtil::createPasswordWithSalt($password);
                $userinfo['created'] = $userinfo['modified'] = date('Y-m-d H:i:s');
                $userinfo['ip'] = $this->getIpClient();
                $userinfo['is_updated_info'] = 0; //Flag update info
                $userinfo['status'] = 0;
                $userinfo['side'] = 'website';
                $return = $model->save($userinfo);
            }

            if ($return['status']) {
                return  $model->getUser($return['id']);
            }
        }

        return array();
    }    
    //End Login facebook

    //--Login Google
    public function apiloginggAction(){
        $params = $this->getParams(self::METHOD_POST_PARAM);
        $googleuser = $params["google_user"];

        if($googleuser){
            $userModel = $this->getUserModel();

            $ggUid = $googleuser["Eea"];
            $userExist = $userModel->getUserByGooogleId($ggUid);

            if ($userExist['social_id']) {
                $userLogin = $userExist;
            } else {
                $userLogin = $this->ggcomplete($googleuser);
            }

                //--Check user valid--
            $resultValid =  $this->checkUserStatus($userLogin);

            if($resultValid['status'] || $resultValid['status_key'] == 'reg_gg' || $resultValid['status_key']=='inactive' ){
                if($resultValid['status']){
                        //-Token login--
                    $token = $this->createdUserToken($userLogin['id'],$userModel);

                    if($token){
                        $userLogin['token'] = $token;
                    }
                        //-End Token login--
                }

                $userLoginFix = $this->apiProcessUser($userLogin);

                $auth = $this->getServiceLocator()->get('FrontAuthService');
                $siteAuthAdapter = new \Core\Auth\Adapter\Social($userModel, 'Google');
                $siteAuthAdapter->setCredential($userLogin);
                $result = $auth->authenticate($siteAuthAdapter);

                if(!$resultValid['status']){
                    if($userLogin['phone']!=''){
                        $this->returnJsonAjax(array('status' => true, 'message' => 'Đăng nhập thành công!','need_active'=>true, 'data' => $userLoginFix));
                    }else{
                        $this->returnJsonAjax(array('status' => true, 'message' => 'Đăng nhập thành công!','need_update'=>true, 'data' => $userLoginFix));
                    }
                }else{
                    $this->returnJsonAjax(array('status' => true, 'message' => 'Đăng nhập thành công!', 'data' => $userLoginFix));
                }


            }else{
                $this->returnJsonAjax($resultValid); 
            }
                //--End check user valid
        }else{
            $this->returnJsonAjax(array('status' => false, 'message' => 'Đăng nhập bằng Google thất bại!'));
        }
        $this->returnJsonAjax(array('status' => false, 'message' => 'Đăng nhập bằng Google thất bại!'));
    }

    public function ggcomplete($data){
        $userinfo = array();

        if ($data) {
            $model = $this->getUserModel();

            $urlAvatarGG = $data["Paa"];
            $email = $data["U3"];;

            $userinfo['social_id'] = $data->id;
            $userinfo['social_picture'] = $urlAvatarGG;
            $socialType = 'Google';

            $userByEmail = $model->getUserByEmail($email);
            if ($userByEmail) { //Exist email
                //$userinfo['activation_code'] = ''; //Auto active
                //$userinfo['mobile_code'] = ''; //Auto active
                //$userinfo['status'] = 1; //Auto active
                if($userByEmail['social_type'] != 'Google'){
                    $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Email_exist')));
                }

                $return = $model->save($userinfo, $userByEmail['id']);
            } else { //Not exist email
                $userinfo['social_type'] = $socialType;
                $userinfo['name'] = $data["ig"];
                $userinfo['first_name'] = $data["ofa"];
                $userinfo['last_name'] = $data["wea"];
                $userinfo['avatar'] = $urlAvatarGG;

                $userinfo['email'] = $email;

                $userinfo['role'] = 10;
                $password = HashUtil::randomPassword();
                $userinfo['password'] = HashUtil::createPasswordWithSalt($password);
                $userinfo['created'] = $userinfo['modified'] = date('Y-m-d H:i:s');
                $userinfo['ip'] = $this->getIpClient();
                $userinfo['is_updated_info'] = 0; //Flag update info
                $userinfo['status'] = 0;
                $userinfo['side'] = 'website';
                $return = $model->save($userinfo);
            }

            if ($return['status']) {
                return  $model->getUser($return['id']);
            }
        }

        return array();
    }

    //--Login thường
    public function apiloginAction(){
        $params = $this->getParams();
        $email = $params["email"];
        $password = $params["password"];
        if($email == ''){
            $this->returnJsonAjax(array('status' => false, 'message' => "Vui lòng nhập email!"));
        }

        if($password == ''){
            $this->returnJsonAjax(array('status' => false, 'message' => "Vui lòng nhập mật khẩu!"));
        }

        if($email && $password){

            $return = $this->auth($password,$email);
            $this->returnJsonAjax($return); //Response Data   
        }
    }

    private function auth($password,$email){
        $data = array('email' => $email, 'password' => $password);

        $auth = $this->getServiceLocator()->get('FrontAuthService');
        $userModel = $this->getUserModel();
        $siteAuthAdapter = new Db($userModel);

        $siteAuthAdapter->setCredential($data);
        $result = $auth->authenticate($siteAuthAdapter);

        $return = array();
        if ($result->isValid()) {
            $user = $siteAuthAdapter->getIdentity();
            if ($user) {
                //--Check user valid--
                $resultValid =  $this->checkUserStatus($user);
                if($resultValid['status']){
                    $return = array('status' => true, 'message' => "Đăng nhập thành công!", 'data' => $this->apiProcessUser($user));
                }else{
                    $return = $resultValid;
                }
                return $return;
                //--End check user valid
            } else {
                $return = array('status' => false, 'message' => "Vui lòng đăng nhập lại!", 'data' => "");
                return $return;

            }
        } else {
            $msg = $result->getMessages();
            $return = array('status' => false, 'message' => $msg[0], 'data' => "");
            return $return;
        }
    }

    //Login by email and password
    private function loginbyemailandpasswordAction($email,$password){
     $data = array('email' => $email, 'password' => $password);

     $auth = $this->getServiceLocator()->get('FrontAuthService');
     $userModel = $this->getUserModel();
     $siteAuthAdapter = new Db($userModel);

     $siteAuthAdapter->setCredential($data);
     $result = $auth->authenticate($siteAuthAdapter);
 }

 private function loginbyuserdata($userLogin,$userModel,$type='Facebook'){
    $userLoginFix = $this->apiProcessUser($userLogin);
    $auth = $this->getServiceLocator()->get('FrontAuthService');
    $siteAuthAdapter = new \Core\Auth\Adapter\Social($userModel, $type);
    $siteAuthAdapter->setCredential($userLogin);
    $result = $auth->authenticate($siteAuthAdapter);
}
   //End login thường

    //Logout--
public function apilogoutAction(){
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $userId = $params['user_id'];
    if($userId){
        $userModel = $this->getUserModel();
        $user = $userModel->getUserByUId($userId);
        if($user){
            $authService = $this->getServiceLocator()->get('FrontAuthService');
            $authService->clearIdentity();
            $this->returnJsonAjax(array('status'=>true,'message'=>'Thoát tài khoản thành công!'));
        }else{
            $this->returnJsonAjax(array('status'=>false,'message'=>'Bạn chưa đăng nhập nên không thể thoát!'));
        }
    }else{
        $this->returnJsonAjax(array('status'=>false,'message'=>'Dữ liệu không hợp lệ!'));  
    }
}

    //Logout--
public function logoutAction(){
    $authService = $this->getServiceLocator()->get('FrontAuthService');
    $authService->clearIdentity();
    return $this->redirect()->toRoute('home', array());
}


    //End Logout--

private function createdUserToken($userId, $userModel){
    if(!$userModel){
        $userModel = $this->getUserModel();
    }

    $token = HashUtil::createRandomKey();
    $saveResult = $userModel->save(array('token'=>$token),$userId);
    if($saveResult['status']){
        return $token;
    }
    return '';
}

    //Profile--
public function apiprofileAction(){
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $userId = $params['user_id'];
    if($params && $userId){
        $userModel = $this->getUserModel();
        $user = $userModel->getUserByUId($userId);

            //--Check user status--
        $checkUser = $this->checkUserStatus($user);
        if(!$checkUser['status']){
            return  $this->returnJsonAjax($checkUser); 
        }
            //--End Check user status--

        if($user){
            $item = $this->apiProcessUser($user);
            $this->returnJsonAjax(array('status'=>true,'message'=>'Lấy dữ liệu thành công!','data'=>$item));
        }
    }

    $this->returnJsonAjax(array('status'=>false,'message'=>'Dữ liệu không hợp lệ!'));
}
    //End Profile--

    //--Update info
public function apiupdateprofileAction(){
    $params = $this->getParams();
    $resultValid = $this->validateUpdateProfile($params);

    if($resultValid['status']){
        $action = $params['act'];
        $userValid = $resultValid['curUser'];

        $userModel = $this->getUserModel();
        $mobileCode = $this->randomCode(4);
        if($action == 'sendcode'){
            $data = array(
                'location'=> $params['location'],
                'is_updated_info' => '1',
                'mobile_code'=> $mobileCode
            );
        }else{
            $data = array(
                'location'=> $params['location']
            );
        }
            //--Update email, phone if email, phone is null---
        $email = $params['email'];
        if($userValid && !$userValid['email'] && $email){
           $data['email'] = $email;
        }

        $phone_code = $params['phonecode'];
        $phone = $phone_code . $this->countryPhoneFix($params['phone'], $phone_code);

        if($userValid && (!$userValid['phone'] || $params['act'] == 'updatephone') && $phone){
           $data['phone'] = $phone;
        }
            //--End Update email, phone if email, phone is null---

        $saveResult = $userModel->save($data, $userValid['id']);

        $smsResult = $this->sendSMS($phone,$mobileCode);
        if($saveResult['status'] && $smsResult['status']){
            $curUser = $userModel->getUser($userValid['id']);
            unset($_SESSION['need_update']);
            $this->returnJsonAjax(array('status'=>true, 'message'=>'Cập nhật thông tin thành công!','data'=>$curUser));
        }else{
            $this->returnJsonAjax(array('status'=>false,'message'=> $this->translate('requesterror')));
        }
    }else{
        $this->returnJsonAjax($resultValid);
    }

$this->returnJsonAjax(array('status'=>false,'message'=>'Dữ liệu không hợp lệ!'));
}

public function apiupdatepasswordAction()
{
    $params = $this->getParams(self::METHOD_POST_PARAM);

    if (!$params['user_token'])
    {
        echo json_encode(array('status' => false, 'message' => 'Bạn cần đăng nhập mới có thể tiếp tục!'));
        exit;
    }

    $userModel = $this->getUserModel();
    $curUser = $userModel->getUserByToken($params['user_token']);
//        echo "".$curUser['password']."<pre>";
//        print_r($params);
//        exit;
    if(!HashUtil::verifyPasswordWithSalt($params['password'],$curUser['password'])){
        echo json_encode(array('status' => false, 'message' => 'Mật khẩu cũ không chính xác!'));
        exit;
    }

    $data = array(
        'password' => HashUtil::createPasswordWithSalt($params['newpassword']),
        );


    $saveResult = $userModel->save($data, $curUser['id']);
    if ($saveResult['status'])
    {
        $curUser = $userModel->getUser($curUser['id']);
        $this->returnJsonAjax(array('status' => true, 'message' => 'Cập nhật thông tin thành công!', 'data' => $this->apiProcessUser($curUser)));
    } else
    {
        $this->returnJsonAjax(array('status' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau!'));
    }


    $this->returnJsonAjax(array('status' => false, 'message' => 'Dữ liệu không hợp lệ!'));
}

private function validateUpdateProfile($params){
    $phone = $params['phone'];
    $phone_code = $params['phonecode'];

    $userModel = $this->getUserModel();
    $curUser = $this->getUserLogin();

    if(!$curUser){
        return array('status'=>false,'message'=>'Bạn cần đăng nhập mới có thể tiếp tục!');
    }/*else{
        //--Check user status--
        $checkUser = $this->checkUserStatus($curUser);
        if(!$checkUser['status']){
            return $checkUser; 
        }
            //--End Check user status--
    }*/

    if($phone){
        if (!is_numeric($phone)) {
            return array('status' => false, 'message' => 'Số điện thoại phải là một chuối số!');
        }

        $minLen = 5;
        if (strlen($phone) < $minLen && substr($phone, 0, 1) == '0') {
            return array('status' => false, 'message' => 'Số điện thoại phải ít nhất '.$minLen.' ký tự!');
        }else if(strlen($phone) < $minLen - 1){
            return array('status' => false, 'message' => 'Số điện thoại phải ít nhất '.$minLen.' ký tự!');
        }

        $maxLen = 13;
        if (strlen($phone) > $maxLen) {
            return array('status' => false, 'message' => 'Số điện thoại phải nhiều nhất '.$maxLen.' ký tự!');
        }

        if($curUser['phone'] != $phone){
            $check = $userModel->isExists('phone', $phone_code . $this->countryPhoneFix($phone, $phone_code));
            //$check = $userModel->isExists('phone', '0' . $this->phoneFix($phone));
            //$check1 = $userModel->isExists('phone', '84' . $this->phoneFix($phone));
            if ($check['status']) {
                return array('status' => false, 'el' => 'phone', 'message' => $this->translate('Existphone'));
            }
        }
    }

    return array('status'=>true,'message'=>'Dữ liệu hợp lệ!' ,'curUser'=>$curUser);
}
    //--End Update info

    //--Forget pass---
public function forgetpassAction(){
    $userModel = $this->getUserModel();
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $email = $params['email'];
    $curLang = $this->getLangCode(true);
    if($email == ''){
        $this->returnJsonAjax(array('status'=>false,'message'=>$this->translate('Validatevalidemail')));
    }else{
        $validator = new \Zend\Validator\EmailAddress();
        if (!$validator->isValid($email)) {
            $this->returnJsonAjax(array('status'=>false, 'message'=>$this->translate('Validatevalidemail')));
        } 

        $isExist = $userModel->isExists('email',$email);
        if(!$isExist['status']){
            $this->returnJsonAjax(array('status'=>false,'message'=>'Email không tồn tại trong hệ thống!'));
            }else{ //Is valid---
                $user = $isExist['item'];
                //--Check user valid--


                    $userId = $user['id'];
                    if($userId){
                        $forgetPassCode = HashUtil::createRandomKey();
                        $resultSave = $userModel->save(array('forget_pass_code'=>$forgetPassCode), $userId);
                        if($resultSave['status']){
                            $urlRecover = BASE_URL;

                            $urlRecover = $urlRecover.'/'.$curLang.'/?fpc='.$forgetPassCode;

                            //--Send email--
                            $mail = $this->getServiceLocator()->get('SendMail');

                            if($curLang=='vi'){
                                $mail->send(array(
                                    'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                                    'to' => array('name' => $user['name'], 'email' => $user['email']),
                                    'subject' => 'VIETJET - NHẬN THÔNG BÁO BẠN ĐÃ QUÊN PASSWORD!',
                                    'template' => 'email/forgotpass',
                                    'data' => array(
                                        'user_name'=>$user['name'],
                                        'url_recover'=>$urlRecover,
                                        'code'=>$forgetPassCode
                                    )
                                ));
                            }else{
                                $mail->send(array(
                                    'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                                    'to' => array('name' => $user['name'], 'email' => $user['email']),
                                    'subject' => 'VIETJET - YOUR PASSWORD HAS BEEN RESET!',
                                    'template' => 'email/forgotpass_en',
                                    'data' => array(
                                        'user_name'=>$user['name'],
                                        'url_recover'=>$urlRecover,
                                        'code'=>$forgetPassCode
                                    )
                                ));
                            }

                            //--End Send email--

                            $this->returnJsonAjax(array('status'=>true,'message'=>'Đã gửi email hướng dẫn lấy lại mật khẩu!'));
                        } 
                    }

                //--End check user valid
         }
     }
 }

 public function recoverAction(){
    $userModel = $this->getUserModel();
    $params = $this->getParams(self::METHOD_POST_PARAM);
    $forgetPassCode = $params['forget_pass_code'];
    $newPass = $params['password'];
    $cfNewPass = $params['cfpassword'];
    $result = $userModel->recover($forgetPassCode, $newPass, $cfNewPass);
     unset($_SESSION['show-fg']);
    $this->returnJsonAjax($result);
}
    //--End Forget pass---

    //--******************** END API **************************:
}
