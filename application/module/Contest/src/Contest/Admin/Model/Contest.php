<?php

namespace Contest\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

class Contest extends AppModel {

    public $table = 'bz1_contests';
    public $context = 'contest';

    public function save($data, $id, $primarykey = 'id') {
        $dateSave = date('Y-m-d H:i:s');
        if ($data['id'] || $id) {
            $data['modified'] = $dateSave;
        } else {
            $data['created'] = $data['modified'] = $dateSave;

        }
       
        return parent::save($data, $id);
    }

	public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $this->setState('filter.search', trim($search));

        $fromDate = $this->getUserStateFromRequest('filter.from_submit_date', 'filter_from_submit_date', '');
        $this->setState('filter.from_submit_date', $fromDate);

        $toDate = $this->getUserStateFromRequest('filter.to_submit_date', 'filter_to_submit_date', '');
        $this->setState('filter.to_submit_date', $toDate);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $type = $this->getUserStateFromRequest('filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $is_win_week = $this->getUserStateFromRequest('filter.is_win_week', 'filter_is_win_week', '');
        $this->setState('filter.is_win_week', $is_win_week);

        $is_win_final = $this->getUserStateFromRequest('filter.is_win_final', 'filter_is_win_final', '');
        $this->setState('filter.is_win_final', $is_win_final);

        parent::populateState();
    }

    public function getDefaultListQuery() {

        $select = new Select($this->table);
        $select->join('bz1_users', 'bz1_users.id=' . $this->table . '.user_id', array('user_created'=>'created','user_name'=>'name','user_email'=>'email','user_phone'=>'phone','user_identify'=>'identify','user_avatar'=>'social_picture'));

        $status = $this->getState('filter.status');
        if (strlen($status) > 0) {
            $select->where(array($this->table.'.status' => $status));
        }

        $keyword = $this->getState('filter.search');
        if ($keyword) {
            $keyword = trim($keyword);
             $select->where(" (". $this->table.".id like '%$keyword%' or ". $this->table.".title like '%$keyword%' or ".$this->table.".user_id like '%$keyword%' or bz1_users.name like '%$keyword%' or bz1_users.email like '%$keyword%' or bz1_users.phone like '%$keyword%') ");
        }

        //--Date user submit contest
        $fromDate = $this->getState('filter.from_submit_date');
        if (!empty($fromDate)) {
            $select->where('date_format('.$this->table.'.created,"%Y-%m-%d") >= "'.$fromDate.'"');
        }

        $toDate = $this->getState('filter.to_submit_date');
        if (!empty($toDate)) {
            $select->where('date_format('.$this->table.'.created,"%Y-%m-%d") <= "'.$toDate.'"');
        }
        //--End Date user submit contest

        $type = $this->getState('filter.type');
        if (!empty($type)) {
            $select->where(array($this->table.'.type' => $type));
        }

        $is_win_week = $this->getState('filter.is_win_week');
        if (isset($is_win_week)) {
            $select->where(array($this->table.'.is_win_week' => $is_win_week));
        }

        $is_win_final = $this->getState('filter.is_win_final');
        if (isset($is_win_final)) {
            $select->where(array($this->table.'.is_win_final' => $is_win_final));
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
              $select->order($this->table.".created DESC");
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

    public function getByIdAndSlug($id, $slug) {
        $select = new Select($this->table);
        $select->join('bz1_users', 'bz1_users.id=' . $this->table . '.user_id', array('user_name'=>'name','user_email'=>'email','user_phone'=>'phone','user_identify'=>'identify','user_avatar'=>'social_picture'));

        //$select->where(array($this->table . '.status' => 1));

        if ($id) {
            $select->where(array($this->table . '.id' => $id));
        }

        if ($slug) {
            $select->where(array($this->table . '.slug' => $slug));
        }

        $select->limit(1);

        //print $select->getSqlString(); die;
        $result = $this->selectWith($select);
        if ($result) {
            $item = $result->current();
            return get_object_vars($item);
        }

        return array();
    }
}