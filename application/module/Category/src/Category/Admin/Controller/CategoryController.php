<?php

namespace Category\Admin\Controller;

use Category\Admin\Form\CategoryForm;
use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;

class CategoryController extends AdminController {

    public $routerName = 'category';
    public function __construct() {
        $this->modelServiceName = 'Category\Admin\Model\Category';
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        parent::onDispatch($e);
        
    }

    public function getForm() {
        $params = $this->getParams();
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Category\Admin\Form\CategoryForm'); 
        $route = $this->params()->fromRoute();
        $action = $this->getEvent()->getRouteMatch()->getParam('action');
        if ($this->getRequest()->isGet() && $action == 'add') {
            if($params['lang']){
                $form->get('language')->setValue($params['lang']);
            }
            $form->bind(new \ArrayObject(array('type' => $route['id'])));
        }
        return parent::setupForm($form);
    }

    public function indexAction() {
        $model = $this->getModel();
        $params = $this->getParams();
        $model->setParams($params);
        $callAction = @$params['callaction'];
        $this->getEventManager()->trigger('onBeforeListing', $this, $params);
        if (trim($callAction)) {
            if (method_exists($this, $callAction)) {
                $return = call_user_func(array($callAction, $this));
                if ($callAction == 'deleteitem') {
                    $return = $this->deleteitem();
                } else {
                    if ($callAction == 'publish') {
                        $return = $this->publish();
                    } else {
                        if ($callAction == 'unpublish') {
                            $return = $this->unpublish();
                        }
                    }
                }
                if (is_array($return) && $return['message']) {
                    $this->addMessage($return['message']);
                }
            } elseif (method_exists($model, $callAction)) {
                $return = call_user_func(array($callAction, $model));
                if (is_array($return) && $return['message']) {
                    $this->addMessage($return['message']);
                }
            } else {
                throw new \Exception('Method Not Exists!');
            }
        }
             
        $items = $model->getTreeData();
        
        return new ViewModel(array(
            'tree' => $items,
            'state' => $model->getStateObject()
        ));
    }

    //-----Process action--------
    public function onBeforeListing(Event $e) {
        $params = $e->getParams();
    }

    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();
    }
    //-----End Process action-------
}

