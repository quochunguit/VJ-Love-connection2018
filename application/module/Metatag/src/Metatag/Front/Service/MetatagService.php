<?php

namespace Metatag\Front\Service;

class MetatagService implements \Zend\EventManager\EventManagerAwareInterface {

    

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

}
