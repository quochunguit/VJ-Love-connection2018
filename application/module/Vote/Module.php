<?php

namespace Vote;

use Vote\Front\Service\VoteService;
use Vote\Admin\Model\Vote as AdminVote;

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

    public function onBootstrap($e) {

        $eventManager = $e->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();
        $listener = new Front\Event\VoteListener();
        $serviceLocator =  $e->getApplication()->getServiceManager();
        $listener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($listener);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(

                'Vote\Front\Service\VoteService' => function($sm) {
                    $service = new VoteService($sm->get('Vote\Front\Model\Vote'));
                    return $service;
                },

                'Vote\Front\Model\Vote' => function($sm) {
                    $model = new Front\Model\Vote();
                    return $model;
                },

                'Vote\Admin\Model\Vote' => function($sm) {
                    $service = new AdminVote($sm->get('Vote\Admin\Model\Vote'));
                    return $service;
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
