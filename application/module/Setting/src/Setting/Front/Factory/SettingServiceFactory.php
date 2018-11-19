<?php
namespace Setting\Front\Factory;

use Setting\Front\Service\SettingService;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class SettingServiceFactory implements FactoryInterface{
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $settingService = new SettingService();
        $settingService->setServiceLocator($serviceLocator);
        
        return $settingService;
        
    }

}
