<?php

namespace Metatag;

use Metatag\Front\Form\MetatagForm;

class Module {

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

    public function onBootstrap($e) {

        $eventManager = $e->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();
        $listener = new Admin\Event\MetatagListener();
        $serviceLocator = $e->getApplication()->getServiceManager();
        $listener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($listener);
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Metatag\Front\Model\Metatag' => function($sm) {

            $metatag = new Front\Model\Metatag();
            return $metatag;
        },
                'Metatag\Admin\Service\MetatagService' => function($sm) {

            $metatag = new Admin\Service\MetatagService($sm->get('Metatag\Admin\Model\Metatag'));
            return $metatag;
        },
                'Metatag\Admin\Model\Metatag' => function($sm) {
            $metatag = new Admin\Model\Metatag();
            return $metatag;
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
