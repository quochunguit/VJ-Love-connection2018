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

             /*** ====fitness==== **/
            'fitness-overview' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/fitness/overview[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Fitness',
                        'action'     => 'overview',
                    ),
                ),
            ),

            'fitness-gallery' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/fitness/gallery[/:tab][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'tab' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Fitness',
                        'action'     => 'gallery',
                    ),
                ),
            ),

            'fitness-servicedetail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/fitness/service/[:slug]-[:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'slug' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Fitness',
                        'action'     => 'servicedetail',
                    ),
                ),
            ),

            'fitness-promotiondetail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/fitness/promotion/[:slug]-[:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'slug' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Fitness',
                        'action'     => 'promotiondetail',
                    ),
                ),
            ),
            /*** ====End fitness ==== **/

            /*** ====residential==== **/
            'residential-overview' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/residential/overview[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Residential',
                        'action'     => 'overview',
                    ),
                ),
            ),

            'residential-gallery' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/residential/gallery[/:tab][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'tab' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Residential',
                        'action'     => 'gallery',
                    ),
                ),
            ),

            'residential-action' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/residential-action[/:action][/:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Residential',
                        'action'     => 'index',
                    ),
                ),
            ),   
            /*** ====End residential==== **/

            /*** ====Retail==== **/
            'retail-promotion' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/promotion[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'promotion',
                    ),
                ),
            ),
            'retail-promotiondetail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/promotion/[:slug]-[:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'slug' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'promotion',
                    ),
                ),
            ),

            'retail-event' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/event[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'event',
                    ),
                ),
            ),

            'retail-eventdetail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/event/[:slug]-[:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'slug' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'event',
                    ),
                ),
            ),

            'retail-brand' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/brand[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'brand',
                    ),
                ),
            ),

            'retail-branddetail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/brand/[:slug]-[:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'slug' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'branddetail',
                    ),
                ),
            ),

            'retail-floorplan' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/floorplan[/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'floorplan',
                    ),
                ),
            ),

            'retail-gallery' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail/gallery[/:tab][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'tab' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'gallery',
                    ),
                ),
            ),

            'retail-action' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/retail-action[/:action][/:id][/]',
                    'constraints' => array(
                        'lang' => '[0-9a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Content\Front\Controller\Retail',
                        'action'     => 'index',
                    ),
                ),
            ),   
            /*** ====End Retail==== **/

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
