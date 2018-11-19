<?php

namespace Vote\Front\Event;

class VoteListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        $this->listeners[] = $events->attach('*', 'onAfterRenderContent', array($this, 'onAfterRenderContent'), 100);
    }

    function onAfterRenderContent(\Zend\EventManager\EventInterface $e) {
        
        $dataType = 'album';
        $event = $e->getName();
        $params = $e->getParams();
        $params['extension'] = $dataType;
        
        $sm = $this->getServiceLocator();
        $voteService = $sm->get('Vote\Front\Service\VoteService');
       
        $authService = $sm->get('AuthService');
        $user = $authService->getIdentity();
        if (!$user) {
            
        } elseif ($user && $user['id']) {
            $isVote = $voteService->isVoted($params['id'], $user['id'], $dataType);
            if (!$isVote) {
                //render vote
                $content = new \Zend\View\Model\ViewModel();
                $content->setTemplate('vote');
                $content->setVariable('item', $params);
                
                echo $sm->get('ViewRenderer')->render($content);
            }
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
