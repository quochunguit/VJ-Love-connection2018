<?php

namespace Metatag\Admin\Event;

class MetatagListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        $this->listeners[] = $events->attach('*', 'onContentPannel', array($this, 'onContentPannel'), 100);
        $this->listeners[] = $events->attach('*', 'onCreate', array($this, 'onCreate'), 100);
        $this->listeners[] = $events->attach('*', 'onEdit', array($this, 'onEdit'), 100);
        $this->listeners[] = $events->attach('*', 'afterDelete', array($this, 'afterDelete'), 100);
    }

    function onContentPannel(\Zend\EventManager\EventInterface $e) {

        $params = $e->getParams();
       
        $sm = $this->getServiceLocator();
        $metatagService = $this->getServiceLocator()->get('Metatag\Admin\Service\MetatagService');
        //render vote
        $content = new \Zend\View\Model\ViewModel();
        $content->setTemplate('metatag_form');
        if($params['id']){
            $metatag = $metatagService->getMetatags($params['id'], $params['type']);
            
            $content->setVariable('metatag', $metatag[0]);
        }else{
            $content->setVariable('metatag', array());
        }
        
        echo $sm->get('ViewRenderer')->render($content);
    }

    function getRequest() {
        $request = $this->getServiceLocator()->get('Request');
        return $request;
    }

    public function onCreate(\Zend\EventManager\EventInterface $e) {
        $this->onEdit($e);
    }

    public function onEdit(\Zend\EventManager\EventInterface $e) {
        $params = $e->getParams();
        $id = $params['id'];
        $type = $params['type'];
        $post = $this->getRequest()->getPost();
        if(!$post['id']){
            $post['id'] = $id;
        }
        if (array_key_exists('meta_title', $post)) {
            $metatagService = $this->getServiceLocator()->get('Metatag\Admin\Service\MetatagService');
            $metatagService->saveMetatag($post);
        }
    }
     public function afterDelete(\Zend\EventManager\EventInterface $e) {
        
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
