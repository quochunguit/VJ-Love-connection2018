<?php

namespace Core\Listener;

class AclListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    const ANOMYOUS_ROLE = '9';

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        
        $this->listeners[] = $events->attach('Zend\Mvc\Controller\AbstractController', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'acl'), 105);
    }

    function acl(\Zend\Mvc\MvcEvent $event) {

        $sm = $this->getServiceLocator();

        $user = $this->getServiceLocator()->get('Site')->getUser();
        $group_id = $user['role'];

        $acl = new \Zend\Permissions\Acl\Acl();
        $this->addRole($acl);
        $this->addResource($event, $acl, $group_id);

        $resource = $this->getRequestResource($event);

        $request = $sm->get('Request');
        $basePath = $request->getBasePath();

        if ($this->getServiceLocator()->get('Site')->isAdmin()) {
            $url = $basePath . '/admin/user/login';
        } else {
            $url = $basePath . '/user/login';
        }
        if (
            ($acl->hasResource($resource) && !$acl->isAllowed($group_id, $resource))
                || !$acl->hasResource($resource)) {
            $this->saveRequestUrl();
            header("Location: " . $url);
            exit();
        }
    }

    protected function addRole($acl) {

        $sm = $this->getServiceLocator();

        $groupModel = $sm->get('User\Admin\Model\Group');
        $groups = $groupModel->findAll(array('order' => 'weight asc'));
        foreach ($groups as $group) {
            $role = new \Zend\Permissions\Acl\Role\GenericRole($group['id']);
            $acl->addRole($role);
        }
    }

    protected function addResource($event, $acl, $group_id) {

        $controller = $event->getTarget();
        $action = $event->getRouteMatch()->getParam('action');
        $action .= 'Action';

        $controllerClass = get_class($controller);

        $pathArray = explode('\\', $controllerClass);
        $module = $pathArray[0];
        $groupPermissionModel = $this->getServiceLocator()->get('User\Admin\Model\GroupPermission');
        $groupsPermission = $groupPermissionModel->findAll(array('wheres' => array(
                'module' => $module,
                'controller' => $controllerClass,
                'permission' => $action,
                'group_id' => $group_id
        )));

        foreach ($groupsPermission as $perm) {
            $resource = $perm['module'] . '-' . $perm['controller'] . '-' . $perm['permission'];
            if (!$acl->hasResource($resource)) {
                $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                $acl->allow($perm['group_id'], $resource);
            }
        }
    }

    protected function getRequestResource(\Zend\Mvc\MvcEvent $event) {
        $controller = $event->getTarget();
        $action = $event->getRouteMatch()->getParam('action');
        $action .= 'Action';

        $controllerClass = get_class($controller);

        $pathArray = explode('\\', $controllerClass);
        $module = $pathArray[0];
        $resource = $module . '-' . $controllerClass . '-' . $action;
        return $resource;
    }

    protected function saveRequestUrl() {
        $sm = $this->getServiceLocator();
        $request = $sm->get('Request');
        $basePath = $request->getBasePath();

        $urlRequest = $request->getUri()->getPath();
        $queryString = $_SERVER['QUERY_STRING'];
        if ($queryString) {
            $urlRequest .='?' . $queryString;
        }
        $container = new \Zend\Session\Container('site');
        $container->redirect = $urlRequest;
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
