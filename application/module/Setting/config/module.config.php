<?php

// module/Setting/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Setting\Admin\Controller\EmailSetting' => 'Setting\Admin\Controller\EmailSettingController',
            'Setting\Admin\Controller\Setting' => 'Setting\Admin\Controller\SettingController',
            'Setting\Admin\Controller\Redirect' => 'Setting\Admin\Controller\RedirectController',
        ),
    ),
    'controller_plugins'=>array(
        'invokables' => array(
            'setting' => 'Setting\Front\Controller\Plugin\Setting',
        )
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'settingadmin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/setting[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Setting\Admin\Controller\Setting',
                        'action' => 'index',
                    ),
                ),
            ),
            'settingadmininfo' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/setting/siteinfo[/]',
                   
                    'defaults' => array(
                        'controller' => 'Setting\Admin\Controller\Setting',
                        'action' => 'info',
                    ),
                ),
            ),
            'emailsettingadmin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/setting/email[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Setting\Admin\Controller\EmailSetting',
                        'action' => 'index',
                    ),
                ),
            ),
            'redirectsettingadmin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/setting/redirect[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Setting\Admin\Controller\Redirect',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
);
