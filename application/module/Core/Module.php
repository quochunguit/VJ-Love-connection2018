<?php

namespace Core;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\EventManager\StaticEventManager;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;
use Core\InputFilter\MyHtmlPurifier;

#use Zend\Db\ResultSet\;

class Module implements AutoloaderProviderInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                    'App' => APPLICATION_LIB . '/App',
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e) {
        $this->bootstrapSession($e);
        $eventManager = $e->getApplication()->getEventManager();

        $sharedManager = $eventManager->getSharedManager();
        //$this->forceHttps();
        //  $eventManager->attach("finish", array($this, "compressOutput"), 100);

        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->event = $eventManager;
        $em = StaticEventManager::getInstance();


        $listener = new Listener\RedirectListener();
        $serviceLocator = $e->getApplication()->getServiceManager();
        $listener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($listener);

        $slistener = new Listener\LayoutListener();
        $slistener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($slistener);

        $alistener = new Listener\AclListener();
        $alistener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($alistener);

        $ulistener = new Listener\UserListener();
        $ulistener->setServiceLocator($serviceLocator);
        $sharedManager->attachAggregate($ulistener);


        $em->attach('Zend\Mvc\Application', 'route', function ($e) {
            $event = $e->getName();
            $target = get_class($e->getTarget());
            $params = $e->getParams();
            $request = $e->getRequest();
            $data = $request->getPost();
            if ($data) {
                $data = $data->toArray();
                /*TODO: can uncomment it to filter data*/
                //$filter = MyHtmlPurifier::getInstance();
                //$data = $filter->autoClean($data);
                $pars = new \Zend\Stdlib\Parameters($data);
                $request->setPost($pars);
            }

            //$output = sprintf('Event "%s" was triggered on target "%s", with parameters %s', $event, $target, json_encode($params));
            // print ($output);
            return true;
        });
    }

    public function forceHttps() {

        if (strtolower($_SERVER['HTTPS']) != 'on' && false) {
            $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            header("Location: $url");
            exit;
        }
    }

    public function bootstrapSession($e) {
        $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    public function compressOutput($e) {
        $response = $e->getResponse();
        $content = $response->getBody();
        $content = str_replace("  ", " ", str_replace("\n", " ", str_replace("\r", " ", str_replace("\t", " ", $content))));

        if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            header('Content-Encoding: gzip');
            $content = gzencode($content, 9);
        }

        $response->setContent($content);
    }

    public function getControllerPluginConfig() {
        
    }

    public function getControllerConfig() {
        
    }

    public function getViewHelperConfig() {
        
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Site' => function($sm) {
            $site = new Service\Site();
            return $site;
        },
                'AuthService' => function($sm) {
            $authService = new AuthenticationService();
            return $authService;
        },
                'FrontAuthService' => function($sm) {
            $authService = $sm->get('AuthService');
            $storage = new SessionStorage('front'.SESSION_PREFIX);
            $authService->setStorage($storage);
            return $authService;
        },
                'AdminAuthService' => function($sm) {
            $authService = $sm->get('AuthService');
            $storage = new SessionStorage('admin'.SESSION_PREFIX);
            $authService->setStorage($storage);
            return $authService;
        },
                'MailTransport' => function ($sm) {
            $config = $sm->get('Config');
            $transport = new Smtp();
            $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));

            return $transport;
        },
                'SendMail' => function($sm) {
            $mail = new Service\SendMail($sm);
            return $mail;
        },
                'ServiceFactory' => function($sm) {
            $fb = new Service\Factory($sm);
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

                if (isset($session['validator'])) {
                    $chain = $sessionManager->getValidatorChain();
                    foreach ($session['validator'] as $validator) {
                        $validator = new $validator();
                        $chain->attach('session.validate', array($validator, 'isValid'));
                    }
                }
            } else {
                $sessionManager = new SessionManager();
            }
            Container::setDefaultManager($sessionManager);
            return $sessionManager;
        },
            ),
        );
    }

}
