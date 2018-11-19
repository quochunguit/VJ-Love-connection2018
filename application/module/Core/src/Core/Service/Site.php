<?php

namespace Core\Service;

class Site implements \Zend\ServiceManager\ServiceLocatorAwareInterface {

    private $serviceLocator;

    const ANOMYOUS_ROLE = '9';

    public function isAdmin() {

        $routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
        //$controller = $this->getServiceLocator()->get('Application')->getMvcEvent()->getTarget();
        //$action = $routeMatch->getParam('action');
        if($routeMatch){
            $controllerClass = $routeMatch->getParam('controller');
        }


        $pathArray = explode('\\', $controllerClass);

        if (strpos(strtolower($controllerClass), "admin") !== false) {
            return true;
        }

        return false;
    }

    public function isSite() {
        return !$this->isAdmin();
    }

    public function getLanguage() {
        
    }

    public function getUser() {

        $sm = $this->getServiceLocator();
        
        if ($this->isAdmin()) {
            $authService = $sm->get('AdminAuthService');
            
        } else {
            $authService = $sm->get('FrontAuthService');
        }
        $user = $authService->getIdentity();
       
        if (!$user['id']) {
            $user = new \ArrayObject(array('id' => 0, 'role' => self::ANOMYOUS_ROLE));
        }
        
        return $user;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
