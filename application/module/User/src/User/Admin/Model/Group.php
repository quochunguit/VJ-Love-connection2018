<?php

namespace User\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;


class Group extends AppModel {

    public $table = 'bz1_groups';
    public $context = 'group';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

       
        parent::populateState();
    }
     public function getDefaultListQuery() {
        $select = new Select($this->table);
        
        $keyword = $this->getState('filter.search');
        if ($keyword) {
            $keyword = trim($keyword);
            $select->where->like("title", "%$keyword%"); 
           
        }
        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }
       
        return $select;
    }

}