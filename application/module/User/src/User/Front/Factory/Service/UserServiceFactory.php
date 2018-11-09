<?php

namespace User\Front\Factory\Service;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use User\Front\Service\UserService;

class UserServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {

        $userService = new UserService();
        $userService->setServiceLocator($serviceLocator);

        return $userService;
    }

}
