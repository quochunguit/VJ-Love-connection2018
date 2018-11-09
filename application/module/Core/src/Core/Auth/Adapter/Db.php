<?php

namespace Core\Auth\Adapter;

use Zend\Authentication\Result;
use App\HashUtil;

class Db extends \Zend\Authentication\Adapter\AbstractAdapter {

    protected $userModel;
    protected $columns = array('username' => 'email', 'password' => 'password','role'=>'10');

    function __construct($userModel = '') {
        $this->userModel = $userModel;
    }

    function getUserModel() {
        return $this->userModel;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function setColumns($columns = '') {
        $this->columns = $columns;
        return $this;
    }

    public function authenticate() {

        $username = $this->credential[$this->columns['username']];
        $password = $this->credential[$this->columns['password']];
        $role = @$this->credential[$this->columns['role']];
        if (empty($username) && empty($password)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Email và mật khẩu không thể trống!'));
        } elseif (empty($username)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Email không thể trống!'));
        } elseif (empty($password)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Mật khẩu không thể trống!'));
        }
        $this->identity = $this->getUserModel()->getItem(array($this->columns['username']=>$username));

        if (!$this->identity) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Email hoặc mật khẩu không đúng!'));
        }
        // if user exists, check password email
        $correct = HashUtil::verifyPasswordWithSalt($password, $this->identity['password']);
        //if password is equals
        if (!$correct) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Email hoặc mật khẩu không đúng!'));
        }

        // if ($this->identity['mobile_code'] && $this->identity['status'] == 0) {
        //     return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Tài khoản chưa được kích hoạt!'));
        // }

        // if (!$this->identity['mobile_code'] && $this->identity['status'] == 0) {
        //     return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Tài khoản đã bị khóa!'));
        // }

        if (($role && strtolower($this->identity['role']) != $role)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Tài khoản này không thể đăng nhập ở đây!'));
        }
        unset($this->identity['password']);

        return new Result(Result::SUCCESS, $this->identity);
    }

}
