<?php
namespace Contest\Front\Model;
use Core\Model\FrontAppModel;
use Zend\Db\Sql\Select;

class Contest extends FrontAppModel {
    public $table = 'bz1_contests';
    public $context = 'contest';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $type = $this->getUserStateFromRequest('filter.type', 'type', '');
        $this->setState('filter.type', $type);

        parent::populateState();
    }

    public function getDefaultListQuery() {

        $select = new Select($this->table);
        $select->join('bz1_users', 'bz1_users.id=' . $this->table . '.user_id', array('name'=>'name'));

        $status = 1; //$this->getState('filter.status');
        if (strlen($status) > 0) {
            $select->where(array($this->table .'.status' => $status));
        }
        
        $keyword = $this->getState('filter.keyword');
        if ($keyword) {
            $keyword = addslashes(trim($keyword));
            //$keyword = mysql_real_escape_string(trim($keyword));
            $select->where("(". $this->table.".title like '%$keyword%')");
        }


        $type = $this->getState('filter.type');
        if (!empty($type)) {
            $select->where(array('type' => $type));
        }

        //order
        $filter_order = $this->getState('order.field');
        if (!empty($filter_order)) {
            $select->order($this->table.'.'.$filter_order.' DESC');
        }
        //$filter_order_dir = $this->getState('order.direction');

        /*if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order($this->table.'.created DESC');
        }*/
        //print $select->getSqlString(); die;
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

        if($ordering){
            $select->order($this->table . "." . $ordering);
        }

        $result = $this->selectWith($select);
        $items = $result->toArray();
        if($limit == 1){
            $items =  $items[0];
        }

        return $items;
    }

    public function getByIdAndSlug($id, $slug) {
        $select = new Select($this->table);
        $select->where(array($this->table . '.status' => 1));

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

    public function getContestByUser($userId, $status = '', $limit = 0) {

        $select = new Select($this->table);
        $select->join('bz1_users', 'bz1_users.id=' . $this->table . '.user_id', array('user_name'=>'name','user_email'=>'email','user_phone'=>'phone','user_identify'=>'identify','user_avatar'=>'social_picture'));

        if (!empty($columns)) {
            $select->columns($columns);
        }

        //$select->where(array($this->table . '.type' => 'contest'));

        if($status != ''){
            $select->where(array($this->table . '.status' => $status));
        }

        if($userId){
            $select->where(array($this->table . '.user_id' => $userId));
        }

        /*if($fromDate){
            $select->where('date_format('.$this->table.'.submit_date,"%Y-%m-%d") >= "'.$fromDate.'"');
        }

        if($toDate){
            $select->where('date_format('.$this->table.'.submit_date,"%Y-%m-%d") <= "'.$toDate.'"');
        }*/

        if (intval($limit) > 0) {
            $select->limit($limit);
        }

        //$select->order($this->table . ".submit_date desc");

        $result = $this->selectWith($select);
        $items = $result->toArray();
        if($limit == 1){
            $items =  $items[0];
        }

        return $items;
    }

    public function limitContestByUser($userId){
        $select = new Select($this->table);
        $select->join('bz1_users', 'bz1_users.id=' . $this->table . '.user_id', array('user_name'=>'name','user_email'=>'email','user_phone'=>'phone','user_identify'=>'identify','user_avatar'=>'social_picture'));

        if (!empty($columns)) {
            $select->columns($columns);
        }

        if($userId){
            $select->where(array($this->table . '.user_id' => $userId));
        }

        $result = $this->selectWith($select);
        $items = $result->toArray();

        return $items;
    }

     /*public function getGroupByChild($type, $limit = 5, $status = 1, $rand = 'RAND()') {
        $limitFix = $limit > 0 ? " LIMIT $limit" : "";
        $statusStr = $status ? " AND p.status='" . $status . "'" : " ";

        $sql = "SELECT ps.id, ps.title, ps.image, ps.created, ps.status, ch.id AS child_id, ch.name AS child_name, ch.`gender` AS child_gender, ch.`birthday` AS child_birthday, ch.`avatar` AS child_avatar
                FROM (
                SELECT p.* FROM bz1_posts AS p              
                WHERE p.type='" . $type . "' 
                " . $statusStr . " ORDER BY " . $rand . $limitFix . "
                ) AS ps
              
                JOIN bz1_users_children AS ch ON ch.id = ps.child_id
                GROUP BY ps.child_id";

        //echo $sql; die;

        $sm = $this->getServiceLocator();
        $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        $statement = $this->adapter->query($sql);
        $results = $statement->execute();
        $rows = $results->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }*/

}