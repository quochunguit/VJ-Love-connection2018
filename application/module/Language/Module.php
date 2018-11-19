<?php
namespace Language;

use Zend\Mvc\ModuleRouteListener;
use Zend\Session\Container;

class Module {

    public function onBootstrap($e)
    {
        // $serviceManager = $e->getApplication()->getServiceManager();

        // $eventManager = $e->getApplication()->getEventManager();
        // $moduleRouteListener = new ModuleRouteListener();
        // $moduleRouteListener->attach($eventManager);
    }

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

    // Add this method:
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Language\Front\Model\Language' => function($sm) {
                    $model = new Front\Model\Language();
                    return $model;
                },       
                'Language\Admin\Form\LanguageForm' => function($sm) {
                    $form = new Admin\Form\LanguageForm();
                    return $form;
                },
                'Language\Admin\Model\Language' => function($sm) {
                    $model = new Admin\Model\Language();
                    return $model;
                },           
            )
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                
            )
        );
    }

}

?>
