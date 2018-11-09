<?php

namespace Contest\Admin\Controller;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;
use Zend\View\Model\ViewModel;

class ContestWeekController extends AdminController {

   public $routerName = 'contestweek';

    public function __construct() {
        $this->modelServiceName = 'Contest\Admin\Model\ContestWeek';
    }

    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Contest\Admin\Form\ContestWeekForm');
        }
        return parent::setupForm($this->form);
    }  

    //-----Process action--------
    public function onBeforeListing(Event $e) {
        $params = $e->getParams();
        if ($params) {
            $model = $this->getModel();

            if (!$params['page']) {
                //Reset filter
                $model->setState('filter.status', '');
                $model->setState('filter.search', '');
                $model->setState('filter.featured', '');
                //End Reset filter
            }

            $type = $params['type'];
            if ($type) {
                $model->setState('filter.type', $type);
            }
        }
    }

    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
        $this->processCropImage('image', $params);   
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();
        $this->processCropImage('image', $params);
    }

    public function onBeforeDelete(Event $e) {
        $params = $e->getParams();
        $this->processDeleteImage(array('image', 'large_image'), $params);
    }
    //-----End Process action-------

}

