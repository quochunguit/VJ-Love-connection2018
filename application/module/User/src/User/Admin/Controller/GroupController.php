<?php
namespace User\Admin\Controller;
use Core\Controller\AdminController;

class GroupController extends AdminController {
    public $routerName = 'group';
    
    public function __construct() {
        $this->modelServiceName = 'User\Admin\Model\Group';
    }
    public function getForm() {
       $form = $this->getServiceLocator()->get('FormElementManager')->get('User\Admin\Form\GroupForm');
        return parent::setupForm($form);
    }

}

