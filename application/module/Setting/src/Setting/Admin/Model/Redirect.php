<?php

namespace Setting\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;

class Redirect extends AppModel {

    public $table = 'bz1_redirects';
    public $context = 'redirect';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);


        $orderField = $this->getUserStateFromRequest('order.field', 'order_field', '');
        $this->setState('order.field', $orderField);

        $orderDir = $this->getUserStateFromRequest('order.direction', 'order_direction', '');
        $this->setState('order.direction', $orderDir);


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
            $select->where->like("source", "%$keyword%"); 
           
        }
        //order
        $filter_order = $this->getState('order.field', 'created');
        $filter_order_dir = $this->getState('order.direction', 'desc');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }

        // print $select->getSqlString();exit;
        return $select;
    }

}
