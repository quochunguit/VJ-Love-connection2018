<?php

// module/Language/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Language\Front\Controller\Language' => 'Language\Front\Controller\LanguageController',
            'Language\Admin\Controller\Language' => 'Language\Admin\Controller\LanguageController',
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'language-switched' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/language-switched[/:code][/]',
                    'constraints' => array(
                        'code' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Language\Front\Controller\Language',
                        'action' => 'switched',
                    ),
                ),
            ),

            'languageadmin' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/language[/:action][/:id][/]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Language\Admin\Controller\Language',
                        'action'     => 'index',
                    ),
                ),
            ),
            
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'vi_VN',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../files',
                'pattern' => '%s.mo',
            ),
        ),
    )
  
);
