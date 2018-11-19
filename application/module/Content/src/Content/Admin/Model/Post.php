<?php

namespace Content\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

class Post extends AppModel {

    public $table = 'bz1_posts';
    public $context = 'post';

    public function save($data) {
        if ($data['id']) {
            $data['modified'] = date('Y-m-d H:i:s');
        } else {
            $data['created'] = date('Y-m-d H:i:s');
            $data['modified'] = $data['created'];
        }
        return parent::save($data);
    }

    public function getItemById($id, $columns = array()) {
        $item = parent::getItemById($id, $columns);
        //TODO: 1 bai n category
        // if ($item) {
        //     $portTermModel = $this->getServiceLocator()->get('Content\Admin\Model\PostTerm');
        //     $item['category'] = $portTermModel->getTermIdsByPostId($item['id']);
        // }
        return $item;
    }

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

        $identity = $this->getUserStateFromRequest('filter.identity', 'filter_identity', '');
        $this->setState('filter.identity', $identity);

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
        
        $category = $this->getState('filter.category');
        if ($category) {
            $select->where(array('category' => $category));
            
            // $category = intval($category);
            // $select->join('bz1_posts_terms', 'bz1_posts_terms.post_id=' . $this->table . '.id', array('term_id'));
            // $select->where(array('term_id' => $category));
        }

        $language = $this->getState('filter.language');
        if (!empty($language) && $language != '*') {
            $select->where(array(' (language = "'.$language.'" or language = "*" )'));
        }

        $type = $this->getState('filter.type');
        if (!empty($type)) {
            $select->where(array('type' => $type));
        }

        $identity = $this->getState('filter.identity');
        if (!empty($identity)) {
            $select->where(array('identity' => $identity));
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
