<?php

// module/Contest/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Contest\Front\Controller\Contest' => 'Contest\Front\Controller\ContestController',
            'Contest\Admin\Controller\Contest' => 'Contest\Admin\Controller\ContestController',
            'Contest\Admin\Controller\ContestWeek' => 'Contest\Admin\Controller\ContestWeekController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'contest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:lang]/contest[/]',
                    'defaults' => array(
                        'controller' => 'Contest\Front\Controller\Contest',
                        'action' => 'index',
                    ),
                ),
            ),

            'contest-submit' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:lang]/contest-submit[/]',
                    'defaults' => array(
                        'controller' => 'Contest\Front\Controller\Contest',
                        'action' => 'submit',
                    ),
                ),
            ),
            'contest-detail' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:lang]/:slug/:id[/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'slug'=> '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Contest\Front\Controller\Contest',
                        'action' => 'submit',
                    ),
                ),
            ),

            //--************************* ADMIN ***************************
            'contest-admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/contest[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Contest\Admin\Controller\Contest',
                        'action' => 'index',
                    ),
                ),
            ),  

            'contestweek-admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/contestweek[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Contest\Admin\Controller\contestweek',
                        'action' => 'index',
                    ),
                ),
            ), 
            //--************************* END ADMIN ***************************   
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'mail/contest/contest' => WEB_ROOT . '/application/templates/front/email/html/contest.phtml',
        )
    ),
);
