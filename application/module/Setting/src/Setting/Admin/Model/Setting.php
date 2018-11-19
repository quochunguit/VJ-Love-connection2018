<?php

namespace Setting\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;

class Setting extends AppModel {

    public $table = 'bz1_settings';
    public $context = 'setting';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);



        $orderField = $this->getUserStateFromRequest('order.field', 'order_field', '');
        $this->setState('order.field', $orderField);

        $orderDir = $this->getUserStateFromRequest('order.direction', 'order_direction', '');
        $this->setState('order.direction', $orderDir);


        parent::populateState();
    }

    public function getDefaultListQuery() {

        $select = new Select($this->table);

        $keyword = $this->getState('filter.search');
        if ($keyword) {
            $keyword = trim($keyword);
            $select->where->like("name", "%$keyword%"); 
        }
        //order
        $filter_order = $this->getState('order.field', 'name');
        $filter_order_dir = $this->getState('order.direction', 'asc');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }

        // print $select->getSqlString();exit;
        return $select;
    }

    public function saveSetting($data) {

        $module = $data['module'];
        $group = $data['group'];
        unset($data['module']);
        unset($data['group']);
        
        foreach ($data as $name => $value) {
            $data = array('name' => $name, 'value' => $value, 'module' => $module, 'group' => $group);
            if (!$this->update($data, array("name = ?" => $name))) {
                $this->insert($data);
            }
        }
    }
    public function getSettingByGroup($group){
        $records = $this->findAll(array('wheres'=>array('group'=>$group)));
        $setting = new \ArrayObject();
        foreach($records as $record){
            $setting[$record['name']] = $record['value'];
        }
        return $setting;
    }
    

}
