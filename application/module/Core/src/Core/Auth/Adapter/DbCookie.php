<?php

namespace Core\Auth\Adapter;

use Zend\Authentication\Result;
use App\HashUtil;

class DbCookie extends \Zend\Authentication\Adapter\AbstractAdapter {

    protected $userModel;
    protected $userTokenModel;
    protected $columns = array('token' => 'token');

    function __construct($userModel, $userTokenModel) {
        if(!$this->userModel){
            $this->userModel = $userModel;
        }

        if(!$this->userTokenModel){
            $this->userTokenModel = $userTokenModel;
        }
    }


    function getUserModel() {
        return $this->userModel;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
        return $this;
    }

    public function authenticate() {
        $token = $this->credential[$this->columns['token']];
        if (empty($token)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Khóa đăng nhập của thành viên không hợp lệ!'));
        }

        $userLogin = $this->getUserModel()->getUserByToken(trim($token));

        $this->identity = $userLogin;

        if (!$this->identity) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Thành viên đăng nhập không tồn tại trong hệ thống!'));
        }

        if ($this->identity['status'] == 2) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Tài khoản đã bị khóa, vui lòng liên hệ với quản trị để biết thêm thông tin!'));
        }

        return new Result(Result::SUCCESS, $this->identity);
    }



}