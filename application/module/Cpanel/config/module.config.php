<?php

// module/Cpanel/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            
            'Cpanel\Admin\Controller\Cpanel' => 'Cpanel\Admin\Controller\CpanelController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin[/]',
                    'defaults' => array(
                        'controller' => 'Cpanel\Admin\Controller\Cpanel',
                        'action'     => 'cpanel',
                    ),
                ),
            ),
            'admin-authen' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/' . ADMIN_SECURE . '[/]',
                    'defaults' => array(
                        'controller' => 'Cpanel\Admin\Controller\Cpanel',
                        'action' => 'adminsecureauthen',
                    ),
                ),
            ),                  
        ),
    ),
 
);
