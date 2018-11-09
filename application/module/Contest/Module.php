<?php

namespace Contest;

use Contest\Front\Form\ContestForm;

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

    // Add this method:
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Contest\Front\Model\Contest' => function($sm) {
                    $contest = new Front\Model\Contest();
                    return $contest;
                },
                'Contest\Admin\Model\Contest' => function($sm) {
                    $contest = new Admin\Model\Contest();
                    return $contest;
                },

                'Contest\Admin\Form\ContestForm' => function($sm) {
                    $form = new Admin\Form\ContestForm();
                    return $form;
                },
                
                'Contest\Admin\Model\ContestWeek' => function($sm) {
                    $contest = new Admin\Model\ContestWeek();
                    return $contest;
                },

                'Contest\Admin\Form\ContestWeekForm' => function($sm) {
                    $form = new Admin\Form\ContestWeekForm();
                    return $form;
                }
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
