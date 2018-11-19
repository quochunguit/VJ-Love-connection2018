<?php

namespace Setting\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;

class RedirectController extends AdminController {

    public $routerName = 'setting/redirect';

    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Setting\Admin\Form\RedirectForm');
        }
        return parent::setupForm($this->form);
    }

    public function getModelServiceName() {
        $this->modelServiceName = 'Setting\Admin\Model\Redirect';
        return $this->modelServiceName;
    }

}
