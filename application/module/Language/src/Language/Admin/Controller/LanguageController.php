<?php
namespace Language\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;

class LanguageController extends AdminController {

    public $routerName ='language';
    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Language\Admin\Form\LanguageForm');
        }
        return parent::setupForm($this->form);
    }

    public function getModelServiceName() {
        $this->modelServiceName = 'Language\Admin\Model\Language';
        return $this->modelServiceName;
    }

    public function switchedAction() {
        $params = $this->getParams();
        $factory = $this->getFactory();
        $factory->adminLanguageContentSet($params['code']);
        //echo $factory->adminLanguageContentGet(); die;

        $url = $_SERVER['HTTP_REFERER'];
        if(strpos($url, 'edit') !== FALSE){
            return $this->redirectToRoute('admin');
        }
        return $this->redirectToUrl($url);
    }
}