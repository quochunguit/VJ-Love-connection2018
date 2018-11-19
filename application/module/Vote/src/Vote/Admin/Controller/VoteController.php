<?php

namespace Vote\Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AdminController;

class VoteController extends AdminController {

  public function __construct() {
        $this->modelServiceName = 'Vote\Admin\Model\Vote';
    }

    public function onBeforeListing(\Zend\EventManager\EventInterface $e) {
        $params = $e->getParams();
        $model = $this->getModel();
        if ($params) {   
            $model->setState('filter.extension', 'contest');
        }
    }

    public function exportAction(){

        $model = $this->getModel();
        $data = $model->getDataExport();

        $excel[] = array(
            'STT', 'Id', 'Contest', 'UserId', 'Name', 'Email', 'Phone', 'Facebook id', 'VoteDate'
        );
        foreach ($data as $key=> $value) {
            $factory = $this->getServiceLocator()->get('ServiceFactory');
            $user = $factory->getUser($value['user_id']);
            $excel[] = array(
                ++$key, 
                $value['id'],
                $value['object_id'],
                $value['user_id'],
                $user['name'],
                $user['email'],
                $user['phone'],
                $user['social_id'],
                $value['created']
            );
        }

        require_once VENDOR_DIR . '/excelwriter/php-excel.class.php';
        $xls = new \Excel_XML('UTF-8', false, 'Posts');
        $xls->addArray($excel);
        $xls->generateXML('UserVote_' . time());
        die;

    }
   
}
