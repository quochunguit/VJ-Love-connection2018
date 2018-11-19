<?php

namespace Contenttype\Admin\Model;
use Zend\Db\Sql\Select;
use Core\Model\AppModel;

class Contenttype extends AppModel {

    public $table = 'bz1_content_types';
    public $context = 'content_types';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

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
            $select->where->like("title", "%$keyword%"); 
        }

        $group = $this->getState('filter.group');
        if ($group) {
           $select->where(array('group' => $group));
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order("group asc");
            $select->order("ordering asc");
            $select->order("id desc");
        }

        return $select;
    }

}
