<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Vote\Front\Controller\Vote' => 'Vote\Front\Controller\VoteController',
            'Vote\Admin\Controller\Vote' => 'Vote\Admin\Controller\VoteController',
        ),
    ),
    'router' => array(
        'routes' => array(
            //----FRONT------
           'vote' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:lang]/api/vote[/]',
                    'defaults' => array(
                        'controller' => 'Vote\Front\Controller\Vote',
                        'action' => 'vote',
                    ),
                ),
            ),
            'share' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/share[/]',
                    'defaults' => array(
                        'controller' => 'Vote\Front\Controller\Vote',
                        'action' => 'share',
                    ),
                ),
            ),
            //----END FRONT------

            //----ADMIN------
            'vote-admin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/vote[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Vote\Admin\Controller\Vote',
                        'action'     => 'index',
                    ),
                ),
            ),
            //----END ADMIN------
        ),
    ),
     'view_manager' => array(
        'template_map' => array(
            'vote' => WEB_ROOT . '/application/module/Vote/src/Vote/Front/View/vote/vote/vote.phtml',
        ),      
    )
);
