<?php

namespace Setting\Front\Factory;

use Setting\Front\View\Helper\Setting;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;


class SettingViewHelperFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $pluginManager) {
        $serviceLocator = $pluginManager->getServiceLocator();
        
        $setting = new Setting();
        
        $settingService = $serviceLocator->get('SettingService');
        $setting->setSettingService($settingService);
        
        return $setting;
    }

}
