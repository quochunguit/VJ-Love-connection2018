<?php

namespace Core\Listener;

class UserListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        $this->listeners[] = $events->attach('*', 'onUserLogin', array($this, 'onUserLogin'), 105);
    }

    function onUserLogin(\Zend\EventManager\EventInterface $event) {
        
        $container = new \Zend\Session\Container('site');
        $urlRequest = $container->redirect;
        
        if (!$urlRequest) {
            $urlRequest = $event->getTarget()->getRequest()->getHeader('HTTP_REFERER', '');
        }
        
        if ($urlRequest) {
            header("Location: " . $urlRequest);
            exit();
        }
    }

    public function detachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
