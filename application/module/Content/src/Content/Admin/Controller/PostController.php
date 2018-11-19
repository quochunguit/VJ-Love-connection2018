<?php
namespace Content\Admin\Controller;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;
use Zend\View\Model\ViewModel;

class PostController extends AdminController {

    public $routerName = 'post';

    public function __construct() {
        $this->modelServiceName = 'Content\Admin\Model\Post';
    }

    public function getForm() {

        $params = $this->getParams();
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Content\Admin\Form\PostForm');
        $route = $this->params()->fromRoute();
        $action = $this->getEvent()->getRouteMatch()->getParam('action');
        if ($this->getRequest()->isGet() && $action == 'add') {
            $form->bind(new \ArrayObject(array('type' => $route['id'])));
        }
        //if ($this->getRequest()->isGet() && $this->params()->fromRoute('id')) {
            //$valueOptions = $form->get('parent_id')->getValueOptions();
            //unset($valueOptions[$route['id']]);
            //$form->get('parent_id')->setValueOptions($valueOptions);
        //}

        return parent::setupForm($form);
    }

    //-----Process action--------
    public function onBeforeListing(Event $e) {
        $params = $e->getParams();
        if ($params) {
            $model = $this->getModel();

            if (!$params['page']) {
                //Reset filter
                $model->setState('filter.identity', '');
                $model->setState('filter.status', '');
                $model->setState('filter.search', '');
                $model->setState('filter.category', '');
                $model->setState('filter.featured', '');
                $model->setState('filter.language', '');
                //End Reset filter
            }

            $type = $params['type'];
            if ($type) {
                $model->setState('filter.type', $type);
            }

            $identity = $params['identity'];
            if ($identity) {
                $model->setState('filter.identity', $identity);
            }

            //--View by lang default
            $factory = $this->getFactory();
            $language = $model->getState('filter.language');
            if(!$language){
                $model->setState('filter.language', $factory->adminLanguageContentGet());
            }
        }
    }

    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
        
        $this->processCropImage('image', $params);
        $this->processCropImage('large_image', $params);
        $this->processMultiImage($params, 'multi_image');
        $this->processMultiImage($params, 'multi_image1');
        $this->processMultiCate($params);
        //$this->processUploadFile($params, 'videos'); //Upload file video
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();

        $this->processCropImage('image', $params);
        $this->processCropImage('large_image', $params);
        $this->processMultiImage($params, 'multi_image');
        $this->processMultiImage($params, 'multi_image1');
        $this->processMultiCate($params);
        //$this->processUploadFile($params, 'videos'); //Upload file video
    }

    public function onGetItem(Event $e) {    
        $params = $e->getParams();
        $this->processMultiCate($params,'category_multi','edit');
    }

    public function onBeforeDelete(Event $e) {
        $params = $e->getParams();
        $this->processDeleteImage(array('image', 'large_image'), $params);
    }
    //-----End Process action-------
}
