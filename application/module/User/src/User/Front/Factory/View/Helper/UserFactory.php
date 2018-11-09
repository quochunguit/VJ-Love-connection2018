<?php

namespace User\Front\Factory\View\Helper;

use User\Front\View\Helper\User;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;


class UserFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $pluginManager) {
        $serviceLocator = $pluginManager->getServiceLocator();
        
        $userHelper = new User();
        
        $userService = $serviceLocator->get('UserService');
        
        $userHelper->setUserService($userService);
        
        return $userHelper;
    }

}
