<?php

namespace User\Front\Block\Latest;

use Block\Front\Block\Type\AbstractType;

class Latest extends AbstractType {

    public function getDefaultParams() {
        return array('template' => 'user/block/latest');
    }

    public function getData() {

        $userService = $this->getServiceLocator()->get('UserService');
        $users = $userService->getLatestUsers(10);
        return $users;
    }

}
