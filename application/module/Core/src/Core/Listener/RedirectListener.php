<?php

namespace Core\Listener;

class RedirectListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        $this->listeners[] = $events->attach('*', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'redirect'), 100);
    }

    function redirect(\Zend\EventManager\EventInterface $event) {


        $sm = $this->getServiceLocator();
        $redirectMapper = $sm->get('Setting\Admin\Model\Redirect');

        $request = $sm->get('Request');
        $basePath = $request->getBasePath();

        $uriRelative = ltrim($request->getUri()->getPath(), $basePath);

        $item = $redirectMapper->findFirst(array('wheres' => array('source' => $uriRelative, 'status' => 1), 'limit' => 1));

        if ($item) {
            $item = array_shift($item);
            $dest = $item['dst'];
            $header = $item['header'];
            $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $dest);
            $response->setStatusCode($header);
            $response->sendHeaders();
            exit;
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
