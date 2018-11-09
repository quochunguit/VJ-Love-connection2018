<?php

namespace Core\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Slug extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

}
