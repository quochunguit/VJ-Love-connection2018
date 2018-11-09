<?php

namespace Cpanel\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;
use Zend\Session\Container;

class CpanelController extends AdminController {
 
    public function cpanelAction(){
        return new ViewModel();
    }
 
	public function adminsecureauthenAction() {
        setcookie(ADMIN_SECURE, ADMIN_SECURE_COOKIE_CODE, 0, "/");
        return $this->redirectToRoute('admin');
    }
}

