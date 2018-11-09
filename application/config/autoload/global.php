<?php
use Zend\Session\SessionManager;
use Zend\Session\Container;

return array(
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'scms',
                //'cookie_secure' => true,
                //'cookie_httponly' => true
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent'
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => WEB_ROOT . '/language/files',
                'pattern' => '%s.mo',
            ),
            array(
                'type' => 'phpArray',
                'base_dir' => WEB_ROOT . '/language/files',
                'pattern' => '%s.php',
            )
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Core\Controller\Core' => 'Core\Controller\CoreController',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Site' => function ($sm) {
                $site = new Core\Service\Site();
                return $site;
            },
            /*
            'CallbackCheckAdapter' => function ($sm) {

                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                $dbTableCheckAdapter = new \Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter(
                    $dbAdapter,
                    'bz1_users',
                    'email',
                    'password',
                    function ($securePass, $password) {
                        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
                        $authenticated = $bcrypt->verify($password, $securePass);
                        return $authenticated;
                    }
                );
                return $dbTableCheckAdapter;
            },*/
            'AuthService' => function ($sm) {
                $authService = new Zend\Authentication\AuthenticationService();
                //set default adapter
                //$authService->setAdapter($sm->get('CallbackCheckAdapter'));
                return $authService;
            },
            'FrontAuthService' => function ($sm) {
                $authService = $sm->get('AuthService');
                $storage = new Zend\Authentication\Storage\Session('front');
                $authService->setStorage($storage);
                return $authService;
            },
            'AdminAuthService' => function ($sm) {
                $authService = $sm->get('AuthService');
                $storage = new Zend\Authentication\Storage\Session('admin');

                $authService->setStorage($storage);
                return $authService;
            },
            'MailTransport' => function ($sm) {
                $config = $sm->get('Config');
                $transport = new Zend\Mail\Transport\Smtp();
                $transport->setOptions(new Zend\Mail\Transport\SmtpOptions($config['mail']['transport']['options']));

                return $transport;
            },
            'SendMail' => function ($sm) {
                $mail = new Core\Service\SendMail($sm);
                return $mail;
            },
            'ServiceFactory' => function ($sm) {
                $fb = new Core\Service\Factory($sm);
                return $fb;
            },
            'Zend\Session\SessionManager' => function ($sm) {
                $config = $sm->get('config');
                if (isset($config['session'])) {
                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                        $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        // class should be fetched from service manager since it will require constructor arguments
                        $sessionSaveHandler = $sm->get($session['save_handler']);
                    }

                    $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                } else {
                    $sessionManager = new SessionManager();
                }
                Container::setDefaultManager($sessionManager);
                return $sessionManager;
            },
        ),
        'aliases' => array(
            'cache' => 'ZendCacheStorageFactory',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formMedia' => 'Core\Form\View\Helper\FormMedia',
            'formInputCustom' => 'Core\Form\View\Helper\FormInputCustom',
        ),
        'factories' => array(
            'absolteUrl' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\AbsoluteUrl($locator->get('Request'));
            },
            'ckeditor' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Ckeditor();
            },
            'formMessenger' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\FormMessenger();
            },
            'plaintext' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Plaintext();
            },
            'shareThis' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Sharethis();
            },
            'slug' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Slug();
            },
            'truncate' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Truncate();
            },
            'flashMessenger' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\FlashMessenger($locator);
            },
            'layoutHelper' => function ($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\LayoutHelper($locator);
            },
            'factory' => function ($sm) {
                $locator = $sm->getServiceLocator();
                $factory = new \Core\View\Helper\Factory($locator->get('Request'));
                $factory->setServiceLocator($locator);
                $factory->setServiceFactory($locator->get('ServiceFactory'));

                return $factory;
            },
            'cropimageupload' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Cropimageupload();
            },
            'multiupload' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Multiupload();
            },

            'fileUpload' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Fileupload();
            },
            'videoUpload' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Videoupload();
            },
            'soundUpload' => function($sm) {
                $locator = $sm->getServiceLocator();
                return new \Core\View\Helper\Soundupload();
            },

        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(),
    ),
);