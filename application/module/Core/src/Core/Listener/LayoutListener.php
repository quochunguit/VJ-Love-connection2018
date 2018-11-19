<?php

namespace Core\Listener;

class LayoutListener implements \Zend\EventManager\SharedListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $listeners = array();
    protected $serviceLocator;

    public function attachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        $this->listeners[] = $events->attach('*', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'layout'), 100);
        $this->listeners[] = $events->attach('*', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'viewTemplate'), 101);

        $this->listeners[] = $events->attach('Zend\Mvc\Controller\AbstractController', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'changeLayout'), 102);
		$this->listeners[] = $events->attach('*', \Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'layout'), 103);
    }

    function changeLayout(\Zend\Mvc\MvcEvent $e) {

        $controller = $e->getTarget();
        $action = $e->getRouteMatch()->getParam('action');

        $controllerClass = get_class($controller);
        
        $pathArray = explode('\\', $controllerClass);
        $module = $pathArray[0];
        //admin layout
        if ($pathArray && (strtolower($pathArray[1]) == 'admin' || strpos(strtolower($controllerClass), "admin") !== false ) ) {
            if ($action == 'login' && strtolower($controllerClass) == 'user\admin\controller\usercontroller') {
                $controller->layout('layout/admin/login');
            } else {
                $controller->layout('layout/admin/layout');
            }
        } else {
            //front layout
            $config = $this->getServiceLocator()->get('Config');
            $layoutConfigs = $config['layout'];
            if (!$layoutConfigs) {
                return;
            }
            foreach ($layoutConfigs as $controllerLayout => $configLayout) {
                if ($controllerLayout === $controllerClass) {
                    if (is_array($configLayout) && !empty($configLayout[$action])) {
                        $controller->layout($configLayout[$action]);
                    } elseif (is_string($configLayout)) {
                        $controller->layout($configLayout);
                    }
                } elseif (strpos($controllerClass, $controllerLayout) !== false) {
                    if (is_array($configLayout) && !empty($configLayout[$action])) {
                        $controller->layout($configLayout[$action]);
                    } elseif (is_string($configLayout)) {
                        $controller->layout($configLayout);
                    }
                } elseif ($controllerLayout == $module) {
                    if (is_array($configLayout) && !empty($configLayout[$action])) {
                        $controller->layout($configLayout[$action]);
                    } elseif (is_string($configLayout)) {
                        $controller->layout($configLayout);
                    }
                }
            }
        }
    }

    function viewTemplate(\Zend\Mvc\MvcEvent $e) {

        $serviceManager = $e->getApplication()->getServiceManager();
        $templatePathResolver = $serviceManager->get('Zend\View\Resolver\TemplatePathStack');

        $action = $e->getRouteMatch()->getParam('action');
        $controller = $e->getRouteMatch()->getParam('controller');
        $pathArray = explode('\\', $controller);
        if ($pathArray[0] == 'Application') {
            if ($pathArray) {
                $path = WEB_ROOT . '/application/module/' . $pathArray[0] . '/src/' . $pathArray[0] . '/View';
                if (is_dir($path)) {
                    $templatePathResolver->addPath($path);
                }
                //override module template
                $path = WEB_ROOT . '/application/templates/front/html';
                if (is_dir($path)) {
                    $templatePathResolver->addPath($path);
                }
            }
        } else {
            if ($pathArray) {
                $path = WEB_ROOT . '/application/module/' . $pathArray[0] . '/src/' . $pathArray[0] . '/' . $pathArray[1] . '/View';
                if (is_dir($path)) {
                    $templatePathResolver->addPath($path);
                }
                //override module template
                $path = WEB_ROOT . '/application/templates/' . strtolower($pathArray[1]) . '/html';
                if (is_dir($path)) {
                    $templatePathResolver->addPath($path);
                }
            }
        }
    }

    function layout(\Zend\EventManager\EventInterface $event) {

        $sm = $this->getServiceLocator();

        $templateName = 'admin';
        $this->addTemplateMap($templateName, $templateName);

        $templateName = 'front';
        $this->addTemplateMap($templateName);

        //TODO: facebook, mobile
        $templateName = 'facebook';
        $this->addTemplateMap($templateName, $templateName);

        $templateName = 'mobile';
        $this->addTemplateMap($templateName, $templateName);
        //TODO: End facebook, mobile
    }

    protected function addTemplateMap($templateName, $container = '') {

        $sm = $this->getServiceLocator();
        $layoutPath = WEB_ROOT . '/application/templates';
        $templateMapResolver = $sm->get('Zend\View\Resolver\TemplateMapResolver');
        
        $folders = array('layout', 'partial', 'error', 'email');
        $layoutPath = WEB_ROOT . '/application/templates';

        foreach ($folders as $folder) {
            if (!is_dir($layoutPath . DS . $templateName . DS . $folder)) {
                continue;
            }

            $dirIterator = \App\FileHelper::getDirectoryIterator($layoutPath . DS . $templateName . DS . $folder);
            foreach ($dirIterator as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                if ($fileInfo->isFile()) {
                    $name = $fileInfo->getBasename('.phtml');
                    if (empty($container)) {
                        $templateMapResolver->add($folder . '/' . $name, $fileInfo->getRealPath());
                    } else {
                        $templateMapResolver->add($folder . '/' . $container . '/' . $name, $fileInfo->getRealPath());
                    }
                }
            }
        }
    }

    protected function addBlockTemplateMap(){
        
    }

    public function detachShared(\Zend\EventManager\SharedEventManagerInterface $events) {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
