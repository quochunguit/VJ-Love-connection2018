<?php

// module/Content/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Content\Front\Controller\Fitness' => 'Content\Front\Controller\FitnessController',
            'Content\Front\Controller\Residential' => 'Content\Front\Controller\ResidentialController',
            'Content\Front\Controller\Retail' => 'Content\Front\Controller\RetailController',
            'Content\Front\Controller\Posts' => 'Content\Front\Controller\PostsController',
            'Content\Front\Controller\Pages' => 'Content\Front\Controller\PagesController',
            'Content\Admin\Controller\Post' => 'Content\Admin\Controller\PostController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(

            //--********** Block Front ***************



            'rules' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/terms&conditions[/]',
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Posts',
                        'action'     => 'rule',
                    ),
                ),
            ),

            'about' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/about[/]',
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Posts',
                        'action'     => 'about',
                    ),
                ),
            ),

            'flightInformation' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/flight-information[/]',
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Posts',
                        'action'     => 'flightinformation',
                    ),
                ),
            ),


            'content' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/content[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Posts',
                        'action'     => 'index',
                    ),
                ),
            ),   

            'news' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/news[/]',
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Posts',
                        'action'     => 'index',
                    ),
                ),
            ),
            //--********** End Block Front ***************

            //--********** Block admin ***************
            'postadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/post[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Admin\Controller\Post',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
     'view_manager'=>array(
        'template_map'=>array(
            'content/block/latest'   => __DIR__ . '/../src/Content/Front/View/content/block/latest/default.phtml',
        )
    )

);
