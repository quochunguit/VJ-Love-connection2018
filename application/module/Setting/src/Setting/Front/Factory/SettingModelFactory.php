<?php
namespace Setting\Front\Factory;

use Setting\Front\Model\Setting;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class SettingModelFactory implements FactoryInterface{
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $setting = new Setting();
        
        return $setting;
        
    }

}
