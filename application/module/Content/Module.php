<?php

namespace Content;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Content\Front\Service\PostService;
use Content\Front\Service\PostTreeService;
use Content\Admin\Model\Post as PostAdmin;
use Content\Admin\Model\PostTerm as PostTermAdmin;
use Content\Admin\Form\PostForm; 
use Content\Admin\Model\Province as ProvinceAdmin;

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

    public function getServiceConfig() {

        return array(
            'factories' => array(
                'Content\Front\Model\Post' => function($sm) {
            $model = new \Content\Front\Model\Post();
            return $model;
        },
                'Content\Front\Service\PostService' => function($sm) {

            $postService = new \Content\Front\Service\PostService();
            return $postService;
        },
                'Content\Admin\Model\Post' => function($sm) {

            $model = new PostAdmin();
            return $model;
        },
         'Content\Admin\Model\Province' => function($sm) {

            $model = new ProvinceAdmin();
            return $model;
        },
                'Content\Admin\Model\PostTerm' => function($sm) {

            $model = new PostTermAdmin();
            return $model;
        },      
                'Content\Admin\Form\PostForm' => function($sm) {
            $form = new PostForm();
            return $form;
        },
                'PostService' => function($sm) {
            $postService = new PostService();
            return $postService;
        },
                'PostTreeService' => function($sm) {
            $postTreeService = new PostTreeService();
            return $postTreeService;
        },
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

}
