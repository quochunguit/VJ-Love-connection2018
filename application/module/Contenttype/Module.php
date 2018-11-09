<?php

namespace Contenttype;

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
                'Contenttype\Admin\Form\ContenttypeForm' => function($sm) {
            $form = new Admin\Form\ContenttypeForm();
            return $form;
        },
                'Contenttype\Admin\Model\Contenttype' => function($sm) {

            $model = new Admin\Model\Contenttype();
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
