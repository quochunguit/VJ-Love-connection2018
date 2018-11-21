<?php

// module/User/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Front\Controller\User' => 'User\Front\Controller\UserController',
            'User\Admin\Controller\User' => 'User\Admin\Controller\UserController',
            'User\Admin\Controller\Group' => 'User\Admin\Controller\GroupController',
            'User\Admin\Controller\Permission' => 'User\Admin\Controller\PermissionController',
        ),
    ),
    
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(

            //************ BLOCK API *************** 

            //Register
            'api-register' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-register[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiregister',
                    ),  
                ),
            ),

            'api-register-verify-sms' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-verify-sms[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiverifysms',
                    ),  
                ),
            ),

            'api-register-resend-sms' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user-resend-sms[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiverifyresendsms',
                    ),  
                ),
            ),
            //End Register

            //Login
            'login' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/login[/]',
                    'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'login',
                    ),
                ),
            ),

            //Login
            'register' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/register[/]',
                    'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'register',
                    ),
                ),
            ),



            'api-login' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user-login[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apilogin',
                    ),  
                ),
            ),

            'api-login-fb' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-login-fb[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiloginfb',
                    ),  
                ),
            ),

            'api-login-gg' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-login-gg[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apilogingg',
                    ),  
                ),
            ),

            'api-logout' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user-logout[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apilogout',
                    ),  
                ),
            ),
            //End login

            //Profile
            'api-profile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user-profile[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiprofile',
                    ),   
                ),
            ),

            'api-update-profile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-update-profile[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'apiupdateprofile',
                    ),   
                ),
            ),
            //End Profile

            //Forget pass
            'user-forget-pass' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-forget-pass[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'forgetpass',
                    ),   
                ),
            ),

            'user-recover' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/[:lang]/user-recover[/]',
                     'defaults' => array(
                        'controller' => 'User\Front\Controller\User',
                        'action'     => 'recover',
                    ),   
                ),
            ),
            //End Forget pass
            //************ END BLOCK API *************** 
            
            //************ BLOCK ADMIN ***************
            'useradmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/user[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Admin\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            'groupadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/group[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Admin\Controller\Group',
                        'action'     => 'index',
                    ),
                ),
            ),
            'permissionadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/permission[/:groupid][/]',
                    'constraints' => array(
                        'groupid'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Admin\Controller\Permission',
                        'action'     => 'index',
                    ),
                ),
            )
            //************ END BLOCK ADMIN ***************
        ),
    ),
     'view_manager'=>array(
        'template_map'=>array(
            'user/block/latest'   => __DIR__ . '/../src/User/Front/View/user/block/latest/default.phtml',
        )
    )
   
);
