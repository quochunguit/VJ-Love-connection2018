<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Factory extends AbstractHelper {

    protected $request;
    protected $serviceLocator;
    protected $serviceFactory;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceFactory() {
        return $this->serviceFactory;
    }

    public function setServiceFactory($serviceFactory) {
        $this->serviceFactory = $serviceFactory;
    }

    public function __invoke() {
        return $this->getServiceFactory();
    }

}