<?php

namespace Language\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;


class Language extends AppModel {

    public $table = 'bz1_languages';
    public $context = 'language';

    
    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);


        $published = $this->getUserStateFromRequest('filter.status', 'filter_status');
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
            $select->where->like("title", "%$keyword%"); 
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');
        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order($this->table. ".created desc");
        }

        return $select;
    }  

    public function getByOptions($limit = 0, $wheres = array(), $ordering = 'created desc', $columns = array()) {

        $select = new Select($this->table);

        if (!empty($columns)) {
            $select->columns($columns);
        }

        $select->where(array($this->table . '.status' => 1)); //Alway get publish

        if($wheres && count($wheres) > 0){
            $select->where($wheres);
        }

        if (intval($limit) > 0) {
            $select->limit($limit);
        }

        if($ordering != "rand()"){
            $select->order($this->table . ".ordering asc");
            $select->order($this->table . "." . $ordering);
        }else{
            $rand = new \Zend\Db\Sql\Expression('RAND()');
            $select->order($rand);
        }

        $result = $this->selectWith($select);
        $items = $result->toArray();
        if($limit == 1){
            $items =  $items[0];
        }

        return $items;
    }
}