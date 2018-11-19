<?php

namespace Category;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Category\Admin\Model\Category as AdminCategory;
use Category\Front\Model\Category;
use Category\Admin\Form\CategoryForm;
class Module implements AutoloaderProviderInterface {

    public function getAutoLoaderConfig() {
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
                'Category\Front\Model\Category' => function($sm) {
                    
                    $model = new Category();
                    return $model;
                },
                'Category\Admin\Model\Category' => function($sm) {
                    
                    $model = new AdminCategory();
                    return $model;
                },
                 'Category\Admin\Form\CategoryForm' => function($sm) {
                    
                    $cf = new CategoryForm($sm);
                    return $cf;
                },   
                 'CategoryService'=>'Category\Front\Factory\Service\CategoryServiceFactory',       
            ),
        );
    }

    public function onBootstrap($e) {
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

}
