<?php

namespace Content\Front\Service;

class PostService implements \Zend\EventManager\EventManagerAwareInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $serviceLocator;
    protected $eventManager;

    public function __construct() {
        
    }

    public function getLatestPosts($type, $limit, $status = null) {

        $options = array(
            'wheres' => array('type' => $type),
            'order' => array('created desc'),
            'limit' => $limit
        );
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }
        $items = $this->getPostMapper()->find('all', $options);
        return $items;
    }

    public function getPopularPosts($type, $limit=5, $status = null) {

        $options = array(
            'wheres' => array('type' => $type),
            'order' => array('views desc'),
            'limit' => $limit
        );
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }

        $items = $this->getPostMapper()->find('all', $options);
        return $items;
    }

    public function getPostsByUser($userId, $type, $limit = 0, $status = null) {

        $options = array(
            'wheres' => array('type' => $type, 'user_id' => $userId),
            'order' => array('created desc'),
        );
        if ($limit > 0) {
            $options['limit'] = $limit;
        }
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }

        $items = $this->getPostMapper()->find('all', $options);
        return $items;
    }

    public function getPostByIdentity($identity, $status = null) {
        $options = array(
            'wheres' => array('identity' => $identity)
        );
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }
        $items = $this->getPostMapper()->find('first', $options);
        return $items[0];
    }

    public function getPostByID($id, $status = null) {
        $options = array(
            'wheres' => array('id' => $id)
        );
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }
        $items = $this->getPostMapper()->find('first', $options);
        return $items;
    }
    public function getMostVotePosts($type, $limit=5, $status = null) {

        $options = array(
            'wheres' => array('type' => $type),
            'order' => array('votes desc'),
            'limit' => $limit
        );
        if ($status !== null) {
            $options['wheres']['status'] = $status;
        }

        $items = $this->getPostMapper()->find('all', $options);
        return $items;
    }
    

    function getPostMapper() {
        return $this->getServiceLocator()->get('Content\Front\Model\Post');
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
