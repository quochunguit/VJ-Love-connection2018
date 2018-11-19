<?php

namespace User\Front\View\Helper;

use Zend\View\Helper\AbstractHelper;

class User extends AbstractHelper{

    protected $userService;
    
    public function __invoke() {
        return $this->getUserService()->getCurrentUser();//getAuthUser();
    }
    public function getUserService() {
        return $this->userService;
    }

    public function setUserService($userService) {
        $this->userService = $userService;
    }  

}
