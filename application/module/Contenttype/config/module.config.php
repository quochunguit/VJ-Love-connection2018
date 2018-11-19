<?php

// module/Contenttype/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            
            'Contenttype\Admin\Controller\Contenttype' => 'Contenttype\Admin\Controller\ContenttypeController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            
            'contenttypeadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/contenttype[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Contenttype\Admin\Controller\Contenttype',
                        'action'     => 'index',
                    ),
                ),
            ),
            
        ),
    ),
   
);
