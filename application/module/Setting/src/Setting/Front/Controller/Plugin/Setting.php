<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Setting\Front\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Setting extends AbstractPlugin {

    public function __invoke($identity) {
        $settingService = $this->getController()->getServiceLocator()->get('SettingService');
        $settingService->setServiceLocator($this->getController()->getServiceLocator());
        return $settingService->getByIdentity($identity);
    }

}
