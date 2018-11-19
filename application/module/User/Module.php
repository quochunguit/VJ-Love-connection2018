<?php

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use User\Front\Model\User;
use User\Admin\Model\User as AdminUser;
use User\Admin\Model\UserLoginLog as AdminUserLoginLog;
use User\Admin\Model\Group;
use User\Admin\Form\UserForm;
use User\Admin\Form\AccountForm;

class Module implements AutoloaderProviderInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'User\Front\Model\User' => function($sm) {
            $user = new User();
            return $user;
        },
                'User\Front\Service\UserService' => function($sm) {

            $userService = new Front\Service\UserService($sm->get('User\Front\Model\User'));
            return $userService;
        },
                'User\Front\Form\AccountForm' => function($sm) {
            $form = new Front\Form\AccountForm();

            return $form;
        },
                'User\Front\Form\RegisterForm' => function($sm) {
            $form = new Front\Form\RegisterForm();

            return $form;
        },
                'User\Front\Form\LoginForm' => function($sm) {
            $form = new Front\Form\LoginForm();

            return $form;
        },
                'User\Front\Form\ForgotForm' => function($sm) {
            $form = new Front\Form\ForgotForm();

            return $form;
        },
                'User\Front\Form\RecoverPasswordForm' => function($sm) {
            $form = new Front\Form\RecoverPasswordForm();

            return $form;
        },
                'User\Front\Form\ChangePasswordForm' => function($sm) {
            $form = new Front\Form\ChangePasswordForm();

            return $form;
        },
                'User\Admin\Model\User' => function($sm) {

            $model = new AdminUser();
            return $model;
        },
         'User\Admin\Model\UserLoginLog' => function($sm) {

            $model = new AdminUserLoginLog();
            return $model;
        },
                'User\Admin\Model\Group' => function($sm) {

            $model = new Group();
            return $model;
        },
                'User\Admin\Model\GroupPermission' => function($sm) {

            $model = new Admin\Model\GroupPermission();
            return $model;
        },
                'User\Admin\Form\UserForm' => function($sm) {

            $userForm = new UserForm();
            return $userForm;
        },
                     'User\Admin\Form\AccountForm' => function($sm) {

            $userForm = new AccountForm();
            return $userForm;
        },
                'UserService' => 'User\Front\Factory\Service\UserServiceFactory'
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'user' => 'User\Front\Factory\View\Helper\UserFactory',
            )
        );
    }

}
