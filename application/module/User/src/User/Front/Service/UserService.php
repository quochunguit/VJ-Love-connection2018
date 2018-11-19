<?php

namespace User\Front\Service;

use \Zend\ServiceManager\ServiceLocatorAwareInterface;
use \Zend\EventManager\EventManagerAwareInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use \Zend\EventManager\EventManagerInterface;

class UserService implements ServiceLocatorAwareInterface, EventManagerAwareInterface {

    protected $userMapper;
    protected $eventManager;
    protected $serviceLocator;

    public function __construct() {
        
    }

    public function register($data) {
        
    }

    public function socialLogin($data) {
        
    }

    public function changeAvatar($data) {
        //move avatar
        $source = WEB_ROOT . DS . 'media' . DS . 'tmp' . DS . 'contest' . DS . $data['avatar'];
        $dest = WEB_ROOT . DS . 'media' . DS . 'user' . DS . 'avatar';
        if (file_exists($source)) {
            \App\FileHelper::mvFile($source, $dest);
        }
        return $this->getUserMapper()->save($data);
    }
    
    public function getLatestUsers($limit, $status=null){
        
         $options = array(
            'order' => array('created desc'),
            'limit'=> $limit
             
        );
         if($status!==null){
             $options['wheres']['status'] = $status;
         }
        $items = $this->getUserMapper()->find('all', $options);
        return $items;
    }

    public function getAuthUser() {
        $user = $this->getServiceLocator()->get('Site')->getUser();
        return $user;
    }

    public function getCurrentUser() {
        $user = $this->getServiceLocator()->get('Site')->getUser();
        if ($user['id']) {
            $currentUser = $this->getUserMapper()->getUser($user['id']);
            if ($currentUser) {
                $truncate = $this->getServiceLocator()->get('viewhelpermanager')->get('Truncate');
                $currentUser->name_fix = $truncate->fixNameLong($currentUser->name, 3, 'last');
            }
            return $currentUser;
        }
    
        return array();
    }

    public function getUserMapper() {
        if(!$this->userMapper){
            $this->userMapper = $this->getServiceLocator()->get('User\Front\Model\User');
        }
        return $this->userMapper;
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
