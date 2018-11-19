<?php

namespace Vote\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

class Vote extends AppModel {

    public $table = 'bz1_votes';
    public $context = 'vote';


    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $language = $this->getUserStateFromRequest('filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        $category = $this->getUserStateFromRequest('filter.category', 'filter_category', '');
        $this->setState('filter.category', $category);

        $type = $this->getUserStateFromRequest('filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $fromdate = $this->getUserStateFromRequest('filter.fromdate', 'filter_fromdate');
        $fromdate = trim($fromdate);
        $this->setState('filter.fromdate', $fromdate);

        $todate = $this->getUserStateFromRequest('filter.todate', 'filter_todate');
        $todate = trim($todate);
        $this->setState('filter.todate', $todate);

        $contestId = $this->getUserStateFromRequest('filter.contest_id', 'filter_contest_id', '');
        $this->setState('filter.contest_id', $contestId);

        parent::populateState();
    }

    public function getDefaultListQuery() {
        $select = new Select($this->table);   
        //$select->join('bz1_users', 'bz1_votes.user_id=bz1_users.id', array('user_name' => 'name', 'user_email' => 'email','user_phone'=>'phone','social_id'=>'social_id'));

        $status = $this->getState('filter.status');
        if (strlen($status) > 0) {
            $select->where(array($this->table.'.status' => $status));
        }

        $contestId = $this->getState('filter.contest_id');
        if($contestId){
             $select->where(array($this->table.'.object_id' => $contestId));
        }

        $type = $this->getState('filter.type');
        if($type){
             $select->where(array($this->table.'.type' => $type));
        }

        $extension = $this->getState('filter.extension');
        if($extension){
             $select->where(array($this->table.'.extension' => $extension));
        }

        $keyword = $this->getState('filter.search');
        if ($keyword) {
            $keyword = trim($keyword);
           $select->where("(".$this->table.".object_id like '%$keyword%' or ". $this->table.".id like '%$keyword%'  or ". $this->table.".user_id like '%$keyword%' ");
        }

        $fromDate = $this->getState('filter.fromdate');
        if (!empty($fromDate)) {
            $select->where('date_format('.$this->table.'.created,"%Y-%m-%d") >= "'.$fromDate.'"');
        }

        $toDate = $this->getState('filter.todate');
        if (!empty($toDate)) {
            $select->where('date_format('.$this->table.'.created,"%Y-%m-%d") <= "'.$toDate.'"');
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order($this->table.'.created desc');
        }
        //print $select->getSqlString();die;
        
        $_SESSION['select_export'] = $select;
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
