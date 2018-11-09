<?php

namespace Contenttype\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;

class ContenttypeController extends AdminController {

    public $routerName ='contenttype';
    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Contenttype\Admin\Form\ContenttypeForm');
        }
        return parent::setupForm($this->form);
    }

    public function getModelServiceName() {
        $this->modelServiceName = 'Contenttype\Admin\Model\Contenttype';
        return $this->modelServiceName;
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

