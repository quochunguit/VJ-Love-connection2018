<?php

namespace User\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;
use App\HashUtil;

class User extends AppModel {

    public $table = 'bz1_users';
    public $context = 'user';

    public function save($data = array()) {

        $id = (int) $data['id'];

        unset($data['cfpassword']);
        if ($data['password']) {
            $hashPassword = HashUtil::createPasswordWithSalt($data['password']);
            $data['password'] = $hashPassword;
        } else {
            unset($data['password']);
        }

        return parent::save($data, $id);
    }

    public function getUserByEmail($email = '') {
        return $this->getItem(array('email' => $email));
    }

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $fromdate = $this->getUserStateFromRequest('filter.fromdate', 'filter_fromdate');
        $fromdate = trim($fromdate);
        $this->setState('filter.fromdate', $fromdate);

        $todate = $this->getUserStateFromRequest('filter.todate', 'filter_todate');
        $todate = trim($todate);
        $this->setState('filter.todate', $todate);

        $group = $this->getUserStateFromRequest('filter.group', 'filter_group', '');
        $this->setState('filter.group', $group);
        
        parent::populateState();
    }

    public function getDefaultListQuery() {
        $select = new Select($this->table);
        $status = $this->getState('filter.status');
        if (strlen($status) > 0) {
            $select->where(array('status' => $status));
        }

        $keyword = $this->getState('filter.search');
        if ($keyword) {
            $keyword = trim($keyword);
            $select->where(" (id like '%$keyword%' or email like '%$keyword%' or name like '%$keyword%' or phone like '%$keyword%' ) ");
        }

        $group = $this->getState('filter.group');
        if ($group) {
            $group = trim($group);
            $select->where(array('role' => $group));
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');
        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
             $select->order("created DESC");
        }
        $_SESSION['select_export'] = $select;
        //print $select->getSqlString();

        return $select;
    }
        public function getDataExport(){
        $select = $_SESSION['select_export'];
        if ($select) {
            $select->limit(10000000);
            return  $this->selectWith($select);
        }
       
        return  null;
    }

}
