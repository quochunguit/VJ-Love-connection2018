<?php

namespace Setting\Front\Service;

use \Zend\EventManager\EventManagerAwareInterface;
use \Zend\ServiceManager\ServiceLocatorAwareInterface;

class SettingService implements EventManagerAwareInterface, ServiceLocatorAwareInterface {

    protected $eventManager;
    protected $serviceLocator;
    protected $settings;

    public function getByIdentity($key) {
        if (!$this->settings) {
            $this->loadSetting();
        }

        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }
        return null;
    }

    protected function loadSetting() {
        $settings = $this->getSettingMapper()->findAll();
        $data = array();
        foreach ($settings as $setting) {
            $key = $setting['group'] . '.' . $setting['name'];
            $data[$key] = $setting['value'];
        }
        $this->settings = $data;
    }

    public function getSettingMapper() {
        if (!@$this->settingMapper) {
            $this->settingMapper = $this->getServiceLocator()->get('SettingModel');
        }
        return $this->settingMapper;
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
