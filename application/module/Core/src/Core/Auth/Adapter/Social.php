<?php
namespace Core\Auth\Adapter;
use Zend\Authentication\Result;

class Social extends \Zend\Authentication\Adapter\AbstractAdapter {

    protected $socialType;
    protected $userModel;

    function __construct($userModel = '', $socialType = '') {
        $this->userModel = $userModel;
        $this->socialType = $socialType;
    }

    public function getUserModel() {
        return $this->userModel;
    }

    public function authenticate() {
        if (!$this->credential) {
            throw new \Exception('Credential can not empty.');
        }
        $email = $this->credential['email'];
        $social_id = $this->credential['social_id'];

        if (!$email) {
            $this->identity = $this->getUserModel()->getItem(array('social_id' => $social_id, 'social_type' => $this->socialType));
        } else {
            $this->identity = $this->getUserModel()->getItem(array('email' => $email));
        }

        if (!$this->identity) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, false);
        }

        //check user is active
        // if (!$this->identity['status']) {
        //   return new Result(Result::FAILURE_CREDENTIAL_INVALID, false, array('Tài khoản của bạn bị khóa. Vui lòng liên hệ với ban quản trị website.'));
        // }

        return new Result(Result::SUCCESS, $this->identity);
    }

}
