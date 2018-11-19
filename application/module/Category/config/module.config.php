<?php

// module/Category/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Category\Front\Controller\Category' => 'Category\Front\Controller\CategoryController',
            'Category\Admin\Controller\Category' => 'Category\Admin\Controller\CategoryController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'category' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/category[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Front\Controller\Category',
                        'action'     => 'index',
                    ),
                ),
            ),
            'categoryadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/category[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Category\Admin\Controller\Category',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
   'view_manager'=>array(
        'template_map'=>array(
            'block/category/default'   => __DIR__ . '/../src/Category/Front/View/category/block/category/default.phtml',
        )
    )
);
