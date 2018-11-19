<?php

namespace Setting\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;

class SettingController extends AdminController {

    public $routerName = 'setting';

    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Setting\Admin\Form\SettingForm');
        }
        return parent::setupForm($this->form);
    }

    public function getModelServiceName() {
        $this->modelServiceName = 'Setting\Admin\Model\Setting';
        return $this->modelServiceName;
    }

    public function infoAction() {
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Setting\Admin\Form\SiteInforForm');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getModel()->saveSetting($data);
            }
        }
        $setting = $this->getModel()->getSettingByGroup('core');
        $form->bind($setting);
        return array('form' => $form);
    }

    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();
    }


}
