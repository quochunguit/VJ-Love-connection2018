<?php

namespace Setting;

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
                'Setting\Admin\Form\SettingForm' => function($sm) {
            $form = new Admin\Form\SettingForm();
            return $form;
        },
                'Setting\Admin\Form\SiteInfoForm' => function($sm) {
            $form = new Admin\Form\SiteInforForm();
            return $form;
        },
                'Setting\Admin\Form\RedirectForm' => function($sm) {
            $form = new Admin\Form\RedirectForm();
            return $form;
        },
                'Setting\Admin\Model\Setting' => function($sm) {
            $contact = new Admin\Model\Setting();
            return $contact;
        },
                'Setting\Admin\Model\Redirect' => function($sm) {
            $contact = new Admin\Model\Redirect();
            return $contact;
        },
                'SettingService' => 'Setting\Front\Factory\SettingServiceFactory',
                'SettingModel' => 'Setting\Front\Factory\SettingModelFactory',
            )
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'setting' => 'Setting\Front\Factory\SettingViewHelperFactory',
            )
        );
    }

}
