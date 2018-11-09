<?php
namespace User\Front\Model;
use Core\Model\FrontAppModel;
use App\HashUtil;

class User extends FrontAppModel {

    public $table = 'bz1_users';
    public $context = 'user';

    public function register($data, $id) {
        if (array_key_exists('cfpassword', $data)) {
            unset($data['cfpassword']);
        }

        $activeCode = HashUtil::createRandomKey();
        $hashPassword = HashUtil::createPasswordWithSalt($data['password']);
        $data['password'] = $hashPassword;
        $data['activation_code'] = $activeCode;
        // $data['status'] = 0;

        $data['created'] = $data['modified'] = date('Y-m-d H:i:s');
        $data['role'] = 10;

        $return = parent::save($data, $id);
        if ($return['status']) {
            $return['activation_code'] = $activeCode;
        }
        return $return;
    }

    public function edit($entry) {
        return parent::save($entry);
    }

    public function getUserByUId($UId, $columns = array()) {
        if($UId){
            return $this->getItem(array('id' => $UId), $columns);
        }
        return array();
    }

    public function getUserByEmail($email, $columns = array()) {
        if($email){
            return $this->getItem(array('email' => $email), $columns);
        }
        return array();
    }
    
    public function getUserByFbUid($fbUid, $columns = array()) {
        if ($fbUid) {
            return $this->getItem(array('social_id' => $fbUid), $columns);
        }
        return array();
    }

    public function getUserByGooogleId($ggUid, $columns = array()){
        if($ggUid){
            return $this->getItem(array('social_id' => $ggUid), $columns);
        }
    }

    public function getUserByToken($token, $columns = array()) {
        if($token){
            return $this->getItem(array('token' => $token), $columns);
        }
        return array();
    }

    public function isEmailExists($email) {
        return $this->isExists('email', $email);
    }

    public function isPhoneExists($phone) {
        return $this->isExists('phone', $phone);
    }

    public function isUsernameExists($username) {
        return $this->isExists('username', $username);
    }

    public function isIdentifyExists($identify) {
        return $this->isExists('identify', $identify);
    }

    public function getUser($id) {
        return $this->getItemById($id);
    }

    public function changePassword($data) {
        if (array_key_exists('cfpassword', $data)) {
            unset($data['cfpassword']);
        }

        $hashPassword = HashUtil::createPasswordWithSalt($data['password']);
        $data['password'] = $hashPassword;

        return parent::save($data);
    }

    public function isCorrectPassword($password, $id) {
        if (empty($password)) {
            return false;
        }
        $item = $this->getItemById($id);
        if ($item) {
            return HashUtil::verifyPasswordWithSalt($password, $item['password']);
        }
        return false;
    }

    /*
    * $fieldCode: mobile_code or activation_code
    */
    public function active($userId,  $fieldCode = "mobile_code") {
        if($userId){
            $data = array(
                'id' => $userId,
                $fieldCode => '',
                'status' => 1
            );

            $return = parent::save($data);
            if($return['status']){
                return array('status'=>true, 'message'=>'Kích hoạt tài khoản thành công!');
            }else{
                return array('status'=>false,'message'=>'Có lỗi xảy ra trong quá trình kích hoạt, vui lòng thử lại!');
            }
        }
    }

    public function recover($code, $newPass, $cfNewPass) {
        if($code == ''){
            return array('status' => false, 'message' => 'Phiên yêu cầu đổi mật khẩu không hợp lệ!');
        }

        if($newPass == ''){
            return array('status' => false, 'message' => 'Vui lòng nhập mật khẩu mới!');
        }

        if($newPass != $cfNewPass){
            return array('status' => false, 'message' => 'Nhập lại mật khẩu mới không khớp!');
        }

        $user = $this->getItem(array('forget_pass_code' => $code));
        if (!$user) {
            return array('status' => false, 'message' => 'Phiên yêu cầu đổi mật khẩu đã hết hạn!');
        }else{
            $hashPassword = HashUtil::createPasswordWithSalt($newPass);
            $result =  parent::save(array('password'=>$hashPassword,'forget_pass_code'=>''), $user['id']);
            if($result['status']){
                return array('status'=>true, 'message'=>'Đổi mật khẩu thành công!');
            }else{
                return array('status'=>true, 'message'=>'Đã có lỗi xảy ra, vui lòng thử lại!');
            } 
        }
    }

}
