<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Session\Container;

class Module
{
    public function onBootstrap($e)
    {
        //$serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    public function getViewHelperConfig() {
        return array(
            'factories' => array(
               'absolteUrl' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\AbsoluteUrl($locator->get('Request'));
            },
            'ckeditor' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Ckeditor();
            },
            'formMessenger' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\FormMessenger();
            },
            'plaintext' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Plaintext();
            },
            'shareThis' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Sharethis();
            },
            'slug' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Slug();
            },
            'truncate' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Truncate();
            },
            'flashMessenger' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\FlashMessenger($locator);
            },
            'layoutHelper' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\LayoutHelper($locator);
            },
            'factory' => function($sm) {
                $locator = $sm->getServiceLocator();
                $factory = new \Core\View\Helper\Factory($locator->get('Request'));
                $factory->setServiceLocator($locator);
                $factory->setServiceFactory($locator->get('ServiceFactory'));

                return $factory;
            },
            ),
                        
        );
    }
}
