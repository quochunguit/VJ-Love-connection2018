<?php

namespace Contest\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

class ContestWeek extends AppModel {

    public $table = 'bz1_contest_weeks';
    public $context = 'contestweek';

	public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $this->setState('filter.search', trim($search));

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

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
             $select->where(" (". $this->table.".id like '%$keyword%' or ". $this->table.".title like '%$keyword%') ");
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');
        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order("ordering ASC");
            $select->order("created DESC");
        }
        
        //print $select->getSqlString();
        return $select;
    }
}