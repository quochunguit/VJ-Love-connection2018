<?php

namespace Core\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\AbstractActionController;

class EventController extends AbstractActionController {

    protected function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();

        $events->attach('onBeforeCreate', array($this, 'onBeforeCreate'));
        $events->attach('onCreate', array($this, 'onCreate'));

        $events->attach('onBeforeDelete', array($this, 'onBeforeDelete'));
        $events->attach('onDelete', array($this, 'onDelete'));

        $events->attach('onBeforeListing', array($this, 'onBeforeListing'));
        $events->attach('onListing', array($this, 'onListing'));

        $events->attach('onBeforeEdit', array($this, 'onBeforeEdit'));
        $events->attach('onEdit', array($this, 'onEdit'));


        $events->attach('onBeforePublish', array($this, 'onBeforePublish'));
        $events->attach('onPublish', array($this, 'onPublish'));
        $events->attach('onAfterPublish', array($this, 'onAfterPublish'));

        $events->attach('onBeforeUnPublish', array($this, 'onBeforeUnPublish'));
        $events->attach('onUnPublish', array($this, 'onUnPublish'));
        $events->attach('onAfterUnPublish', array($this, 'onAfterUnPublish'));

        $events->attach('onBeforeGetItem', array($this, 'onBeforeGetItem'));
        $events->attach('onGetItem', array($this, 'onGetItem'));
    }

    protected function prepareArgs($data = array()) {
        if ($data instanceof \ArrayObject) {
            $data = $data->getArrayCopy();
        }
        return $this->getEventManager()->prepareArgs($data);
    }

    public function onBeforeCreate(Event $e) {
        
    }

    public function onCreate(Event $e) {
        
    }

    public function onBeforeListing(Event $e) {
        
    }

    public function onListing(Event $e) {
        
    }

    public function onBeforeDelete(Event $e) {
        
    }

    public function onDelete(Event $e) {
        
    }

    public function onBeforeEdit(Event $e) {
        
    }

    public function onEdit(Event $e) {
        
    }

    public function onBeforePublish(Event $e) {
        
    }

    public function onPublish(Event $e) {
        
    }

    public function onAfterPublish(Event $e) {
        
    }

    public function onBeforeUnPublish(Event $e) {
        
    }

    public function onUnPublish(Event $e) {
        
    }

    public function onAfterUnPublish(Event $e) {
        
    }

    public function onBeforeGetItem(Event $e) {
        
    }

    public function onGetItem(Event $e) {
        
    }

}
