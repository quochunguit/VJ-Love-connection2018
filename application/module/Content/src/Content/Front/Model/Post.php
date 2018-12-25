<?php

namespace Content\Front\Model;

use Core\Model\FrontAppModel;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class Post extends FrontAppModel {

    public $table = 'bz1_posts';
    public $context = 'post';

    public function populateState($ordering = null, $direction = null) {

        $search = $this->getUserStateFromRequest('filter.search', 'filter_search');
        $search = trim($search);
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $language = $this->getUserStateFromRequest('filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        $category = $this->getUserStateFromRequest('filter.category', 'id', '');
        $this->setState('filter.category', $category);

        $type = $this->getUserStateFromRequest('filter.type', 'type', '');
        $this->setState('filter.type', $type);

        parent::populateState();
    }

    public function getDefaultListQuery() {

        $select = new Select($this->table);
        $status = 1; //$this->getState('filter.status');
        if (strlen($status) > 0) {
            $select->where(array('status' => $status));
        }
        
        $keyword = $this->getState('filter.keyword');
        if ($keyword) {
            $keyword = addslashes(trim($keyword));
            //$keyword = mysql_real_escape_string(trim($keyword));
            $select->where("(". $this->table.".title like '%$keyword%')");
        }

        $category = $this->getState('filter.category');
        if ($category) {
            $category = intval($category);
            $select->join('bz1_posts_terms', 'bz1_posts_terms.post_id=' . $this->table . '.id', array('term_id'));
            $select->where(array('term_id' => $category));
        }

        $type = $this->getState('filter.type');
        if (!empty($type)) {
            $select->where(array('type' => $type));
        }

        $language = $this->getState('filter.language');
        if (!empty($language) && $language != '*') {
            $select->where(array(' (language = "'.$language.'" or language = "*" )'));
        }

        $identity = $this->getState('filter.identity');
        if(!empty($identity)){
            $select->where(array('identity' => $identity));
        }

        //order
        $filter_order = $this->getState('order.field');
        $filter_order_dir = $this->getState('order.direction');

        if (!empty($filter_order)) {
            $select->order($filter_order . " " . $filter_order_dir);
        }else{
            $select->order($this->table.'.created DESC');
        }
        //print $select->getSqlString(); die;
        return $select;
    }

    public function getByOptions($type = 'news', $limit = 0, $language='*', $wheres = array(), $ordering = 'created desc', $columns = array()) {

        $select = new Select($this->table);

        if (!empty($columns)) {
            $select->columns($columns);
        }

        $select->where(array($this->table . '.status' => 1)); //Alway get publish

        if($type){
            $select->where(array($this->table . '.type' => $type));
        }

        if (!empty($language) && $language != '*') {
            $select->where(array(' ('.$this->table.'.language = "'.$language.'" or '.$this->table.'.language = "*" )'));
        }

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

    public function getByOptionsSearch($type = 'news', $limit = 0, $language='*', $keyword='', $wheres = array(), $ordering = 'created desc', $columns = array()) {

        $select = new Select($this->table);

        if (!empty($columns)) {
            $select->columns($columns);
        }

        $select->where(array($this->table . '.status' => 1)); //Alway get publish

        if ($keyword) {
            $keyword = addslashes(trim($keyword));
            //$keyword = mysql_real_escape_string(trim($keyword));
            $select->where("(". $this->table.".title like '%$keyword%')");
        }


        if($type){
            $select->where(array($this->table . '.type' => $type));
        }

        if (!empty($language) && $language != '*') {
            $select->where(array(' ('.$this->table.'.language = "'.$language.'" or '.$this->table.'.language = "*" )'));
        }

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

    public function getByMultiCate($type = 'news', $limit = 0, $language='*', $category='', $wheres = array(), $ordering = 'created desc', $columns = array()) {

        $select = new Select($this->table);

        if (!empty($columns)) {
            $select->columns($columns);
        }

        $select->where(array($this->table . '.status' => 1)); //Alway get publish

        if ($category) {
            $select->where("(". $this->table.".category_multi like '%$category%')");
        }


        if($type){
            $select->where(array($this->table . '.type' => $type));
        }

        if (!empty($language) && $language != '*') {
            $select->where(array(' ('.$this->table.'.language = "'.$language.'" or '.$this->table.'.language = "*" )'));
        }

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

    public function getByIdAndSlug($id, $slug, $language = '*') {
        $select = new Select($this->table);
        $select->where(array($this->table . '.status' => 1));

        if ($id) {
            $select->where(array($this->table . '.id' => $id));
        }

        if ($slug) {
            $select->where(array($this->table . '.slug' => $slug));
        }

        if (!empty($language) && $language != '*') {
            $select->where(array(' ('.$this->table.'.language = "'.$language.'" or '.$this->table.'.language = "*" )'));
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

    public function getAllContentTypePost($content_type){
        $select = new Select($this->table);
        if($content_type){
            $select->where(array($this->table . '.type' => $content_type));
        }
        $result = $this->selectWith($select);
        $items = $result->toArray();
        return $items;
    }

}
