<?php

namespace User\Admin\Controller;

use Core\Controller\AdminController;

class PermissionController extends AdminController {

    public $routerName = 'permission';

    public function __construct() {
        $this->modelServiceName = 'User\Admin\Model\GroupPermission';
    }

    public function indexAction() {

        $serviceLocator = $this->getServiceLocator();

        $groupModel = $serviceLocator->get('User\Admin\Model\Group');

        $groupPermissionModel = $serviceLocator->get('User\Admin\Model\GroupPermission');


        if ($this->getRequest()->isPost()) {

            $postData = $this->getRequest()->getPost();
            $postData = $postData['perm'];

            foreach ($postData as $key => $val) {
                $perm = explode('-', $key);
                $module = array_shift($perm);
                $controller = array_shift($perm);
                $action = array_shift($perm);
                $group_id = array_shift($perm);

                $data = array(
                    'module' => $module,
                    'controller' => $controller,
                    'permission' => $action,
                    'group_id' => $group_id
                );
                
                $groupPermissionModel->delete($data);
                $groupPermissionModel->save($data);
            }
        }

        $groups = $groupModel->findAll(array('order' => 'weight asc'));
        $groupsPermission = $groupPermissionModel->findAll();

        $perms = array();
        foreach ($groupsPermission as $perm) {
            $perms[$perm['module']][$perm['controller']][$perm['permission']][$perm['group_id']] = $perm['group_id'];
        }
        
        function endsWith($haystack, $needle) {
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }
            return (substr($haystack, -$length) === $needle);
        }

        $excludeActions = array('notFoundAction', 'getMethodFromAction');
        $modules = $serviceLocator->get('modulemanager')->getLoadedModules();
        $data = new \ArrayObject();
        foreach ($modules as $moduleName => $module) {
            $moduleConfig = $module->getConfig();
            $invokables = $moduleConfig['controllers']['invokables'];
            $controllerData = array();
            foreach ($invokables as $invokable => $controller) {
                $actions = array();
                $methods = get_class_methods($controller);
                if($methods && is_array($methods)){
                foreach ($methods as $method) {
                    if (endsWith($method, 'Action') && !in_array($method, $excludeActions)) {
                        $action = array('action' => $method);
                        foreach ($groups as $group) {
                            if (@$group['id'] == @$perms[$moduleName][$controller][$method][@$group['id']]) {
                                $action['group'][$group['id']] = 1;
                            } else {
                                $action['group'][$group['id']] = 0;
                            }
                        }

                        $actions[] = $action;
                    }
                }
                }
                $controllerData[] = array('controller' => $controller, 'action' => $actions);
            }
            $data[] = array('module' => $moduleName, 'controller' => $controllerData);
        }

        $view = new \Zend\View\Model\ViewModel(array('data' => $data, 'group' => $groups, 'perm' => $perms));
        return $view;
    }

}
