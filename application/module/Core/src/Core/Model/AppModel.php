<?php

namespace Core\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Core\Model\Pagination;

abstract class AppModel extends AbstractTableGateway implements \Zend\ServiceManager\ServiceLocatorAwareInterface {

    public $state;
    public $params = array();
    public $context = 'app';
    public $pagination;
    public $limit = 20;
    public $page = 1;
    public $table = '';
    public $total = 0;
    public $listQuery = '';
    protected $sqlString;

    public function initialize() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $this->adapter = $adapter;
        parent::initialize();
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            //  throw new \Exception('Invalid content property');
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            // throw new \Exception('Invalid content property');
        }

        return $this->$method();
    }

    public function getContext() {
        $ctx = $this->context;

        if ($this->getParam('action')) {
            $ctx .= '_' . $this->getParam('action');
        }
        return $ctx;
    }

    public function setContext($context) {
        $this->context = $context;
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParam($key) {
        if(array_key_exists($key, $this->params)){
            return $this->params[$key];
        }
        return null;
    }

    public function setParam($key, $value) {
        $this->params[$key] = $value;
    }

    public function getItem($where = array(), $columns = array()) {
        $select = new Select($this->table);
        if (!empty($columns)) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }

        $select->limit(1);
        $result = $this->selectWith($select);
        return $result->current();
    }

    public function getAllItems($where = array(), $columns = array(), $order = 'id desc') {
        $select = new Select($this->table);
        if (!empty($columns)) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }

        if($order){
            $select->order($order);
        }

        $result = $this->selectWith($select);
        return $result;
    }

    public function getAllItemsToArray($where = array(), $columns = array()) {
        $result = $this->getAllItems($where, $columns);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

   public function getAllItemsToArrayFix($where = array(), $columns = array()) {
        if(!in_array('status', $where)){
            $where['status'] = 1;
        }

        $result = $this->getAllItems($where, $columns);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    public function getAllItemsToKeyVal($where = array(), $options = array('key' => 'id', 'value' => 'title'), $order = '') {
        $columns = array($options['key'], $options['value']);
        $result = $this->getAllItems($where, $columns, $order);
        $data = array();
        if ($result) {
            foreach ($result as $row) {
                $data[$row[$options['key']]] = $row[$options['value']];
            }
        }
        return $data;
    }

    public function getItemById($id, $columns = array()) {
        $result = $this->getItem(array('id' => intval($id)), $columns);
         if($result && array_key_exists('params', $result)){
            $params = json_decode($result['params']);
            $result['params'] = new \ArrayObject((array)$params);
        }
        return $result;
    }

    public function getListItems($where = array(), $columns = array(), $limit = 0) {
        $select = new Select($this->table);
        if (!empty($columns)) {
            $select->columns($columns);
        }
        $select->where($where);
        if (intval($limit) > 0) {
            $select->limit($limit);
        }

        $result = $this->selectWith($select);
        return $result;
    }

    public function getItems() {
        $this->populateState();
        $this->total = $this->getTotal();

        $limit = $this->getLimit();
        $this->pagination = new Pagination($this->total, $limit, $this->getPage());
        $select = $this->getListQuery();
        $items = $this->getList($select, $this->getstart(), $limit);

        return $items;
    }

    public function getPagination() {
        
    }

    public function setPagination() {
        
    }

    public function getList($select, $start, $limit) {
        if ($this->getLimit()) {
            $select->offset($start);
            $select->limit($limit);
        }

        $result = $this->selectWith($select);
        return $result;
    }

    public function populateState($ordering = null, $direction = null) {

        $filter_order = $this->getUserStateFromRequest('order.field', 'filter_order');
        $this->setState('order.field', $filter_order);

        $filter_order_dir = $this->getUserStateFromRequest('order.direction', 'filter_order_Dir');
        $this->setState('order.direction', $filter_order_dir);
    }

    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true) {
        if (array_key_exists($request, $this->params)) {
            return $this->params[$request];
        } else {
            return $this->getState($key, $default);
        }
    }

    public function getState($property = null, $default = null) {

        return $property === null ? $this->getStateObject() : $this->getStateObject()->get($property, $default);
    }

    public function setState($property, $value = null) {

        return $this->getStateObject()->set($property, $value);
    }

    public function getStateObject() {
        if (!$this->state) {
            $this->state = new AppState($this->getContext());
        }
        return $this->state;
    }

    public function getListQuery() {
        $this->listQuery = $this->getDefaultListQuery();
        return $this->listQuery;
    }

    /**
     * override this function for select db
     * @return \Zend\Db\Sql\Select
     */
    public function getDefaultListQuery() {
        $select = new Select($this->table);
        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }

        return $select;
    }

    public function setListQuery($select) {
        $this->listQuery = $select;
    }

    public function getListCount($select) {

        // $countSelect = new Select();
        $countSelect = $select;
        $countSelect->reset(Select::COLUMNS);
        $countSelect->columns(array(new Expression('count(*) as numrow')));
        $resultSet = $this->selectWith($countSelect);
        $data = $resultSet->toArray();

        return $data[0]['numrow'];
    }

    public function getTotal() {
        $select = $this->getListQuery();
        $total = $this->getListCount($select);
        return $total;
    }

    public function getStart() {
        $itemPerPage = $this->getLimit();
        if (array_key_exists('page', $this->params)) {
            $page = intval($this->params['page']);
            if ($page > 1) {
                return floor($itemPerPage * ($page - 1));
            }
        }
        return 0;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
    }

    public function getCount() {
        
    }

    public function getPage() {
        $page = intval(@$this->params['page']);

        if (empty($page)) {
            $this->page = 1;
        } else {
            $this->page = $page;
        }
        return $this->page;
    }

    public function setPage($page = 1) {
        $this->page = $page;
    }

    public function getPaging() {
        return $this->pagination;
    }

    public function save($data, $id = 0, $primarykey = 'id') {
        if (is_a($data, 'ArrayObject')) {
            $data = $data->getArrayCopy();
        }
        if (empty($id) && $data[$primarykey]) {
            $id = $data[$primarykey];
        }
        
        if($data && array_key_exists('params', $data)){
            $data['params'] = json_encode($data['params']);
        }
        
        if (empty($id)) {

            if (array_key_exists("$primarykey", $data)) {
                unset($data["$primarykey"]);
            }
            if (array_key_exists('created', $data) && empty($data['created'])) {
                $data['created'] = date('Y-m-d H:i:s');
            }
            if (array_key_exists('modified', $data) && empty($data['modified'])) {
                $data['modified'] = date('Y-m-d H:i:s');
            }
            if ($this->insert($data)) {
                $id = $this->getLastInsertValue();
            }
        } else {
            if (array_key_exists('modified', $data) && empty($data['modified'])) {
                $data['modified'] = date('Y-m-d H:i:s');
            }

            $this->update($data, array("$primarykey = ?" => $id));
        }
        if ($id) {
            $data['id'] = $id;
            return array(
                'status' => true, 'id' => $id, 'item' => $data
            );
        } else {
            return array('status' => false);
        }
    }

    public function increase($where = array(), $fieldIncr = '') {
        $data = array(
            "$fieldIncr" => new Expression("$fieldIncr+1")
        );
        return $this->update($data, $where);
    }

    public function decrease($where = array(), $fieldIncr = '') {
        $data = array(
            "$fieldIncr" => new Expression("$fieldIncr-1")
        );
        return $this->update($data, $where);
    }

    public function publish() {
        $data = $this->params['data']['Choose'];
        $status = 1;
        if ($this->changeStatus($data, $status)) {
            return array('status' => true, 'message' => 'Published successful!');
        }
    }

    public function unpublish() {
        $data = $this->params['data']['Choose'];
        $status = 0;
        if ($this->changeStatus($data, $status)) {
            return array('status' => true, 'message' => 'Unpublished successful!');
        }
    }

    public function deleteItem() {
        $data = $this->params['data']['Choose'];
        $id_str = implode(array_map('intval', $data), ',');
        if ($id_str) {
            $where = "id in ($id_str)";
            if ($this->delete($where)) {
                return array('status' => true, 'message' => 'Delete successful!');
            }
        }
    }

    public function deleteItemBy($field, $value = null) {
        if ($value) {
            $where = $field . " in ($value)";
            if ($this->delete($where))
                return array('status' => true, 'message' => 'Delete successful!');
        }
    }

    public function changeStatus($ids, $newstatus) {
        $data = array(
            'status' => $newstatus
        );
        $id_str = implode(array_map('intval', $ids), ',');
        if ($id_str) {
            return $this->update($data, "id in ($id_str)");
        }
    }

    public function isExists($field, $value) {
        $item = $this->getItem(array("$field" => $value));
        if ($item && $item['id']) {
            return array('status'=>true, 'item'=>$item);
        }
        return array('status'=>false);
    }

    public function toArray($entry) {
        if (is_a($entry, 'ArrayObject')) {
            $data = $entry->getArrayCopy();
        } else {
            $data = $entry;
        }
        return $data;
    }

    public function debugQuery() {
        return $this->sqlString;
    }

    /**
     * 
     * @param string $type all|first|count
     * @param array $options   fields|wheres|order|joins|limit|start|groups
     * @return type
     */
    public function find($type, $options = array()) {
        $type = strtolower($type);
        $typeOptions = array('all', 'first', 'one', 'count');
        if (!in_array($type, $typeOptions)) {
            throw new \Exception('Invalid Type Query');
        }

        $fields = @$options['fields'];
        $wheres = @$options['wheres'];
        $order = @$options['order'];
        $joins = @$options['joins'];
        $limit = @$options['limit'];
        $start = @$options['start'];
        $groups = @$options['groups'];

        $table = $this->table;
        if (@$options['table']) {
            $table = $options['table'];
        }
        $select = new Select($table);
        if ($fields)
            $select->columns($fields);

        if ($joins) {
            foreach ($joins as $key => $join) {
                $field = '*';
                if (isset($join['fields']))
                    $field = $join['fields'];
                $select->join($join['table'], $join['conditions'], $field, $join['type']);
            }
        }

        if ($wheres)
            $select->where($wheres);

        if (!empty($order))
            $select->order($order);

        if ($groups) {
            $select->group($groups);
        }

        if ($type == 'first' || $type == 'one') {
            $limit = 1;
        }
        if ($start) {
            $select->offset($start);
        }
        if ($limit) {
            $select->limit($limit);
        }

        // $this->sqlString = $select->getSqlString();

        $result = $this->selectWith($select);
        if ($type == 'count') {
            return $result->count();
        }
        return $result->toArray();
    }

    public function findAll($options = array()) {
        $type = 'all';
        return $this->find($type, $options);
    }

    public function findFirst($options = array()) {
        $type = 'first';
        $options['limit'] = 1;
        return $this->find($type, $options);
    }

    public function findOne($options = array()) {
        $type = 'first';
        return $this->find($type, $options);
    }

    public function findCount($options = array()) {
        $type = 'count';
        return $this->find($type, $options);
    }

}
