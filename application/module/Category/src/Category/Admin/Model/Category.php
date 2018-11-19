<?php

namespace Category\Admin\Model;

use Core\Model\AppModel;


class Category extends AppModel {

    public $table = 'bz1_terms';
    public $context = 'term';

    public function populateState($ordering = null, $direction = null) {

        $published = $this->getUserStateFromRequest('filter.status', 'filter_status', '');
        $this->setState('filter.status', $published);

        $language = $this->getUserStateFromRequest('filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        $type = $this->getUserStateFromRequest('filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        parent::populateState();
    }

    public function getTreeData($lang = '') {
        //root
        $root = $this->getRoot($lang);
        //second level
        $childs = $this->getChild($root);

        return $childs;
    }

    public function getRoot($lang = '') {

        $this->populateState();

        $wheres = array('parent_id' => '0');

        $type = $this->getState('filter.type');
        if($_SESSION['content_type'] || $type){
            $type = $type ? $type : $_SESSION['content_type'];
            $wheres['type'] = $type;
        }

        if($lang){
             $wheres['language'] = $lang;
        }else{
            $langFilter =  $this->getState('filter.language');
            if(!empty($langFilter) && $langFilter != '*'){
                $wheres['language'] = $langFilter;
            }
        }

        $status =  $this->getState('filter.status');
        if(strlen($status) > 0){
            $wheres['status'] = $status;
        }
        
        $options = array(
            'wheres'=> $wheres ,
            'order'=> array('ordering' => 1)
        );

        $root = $this->find('all',$options);

     return $root;
    }

    public function getRootOptions($lang = '') {
        $root = $this->getRoot($lang);

        $options = array();
        foreach ($root as $key => $cat) {
            $options[$cat['id']] = $cat['title'];
        }

        return $options;
    }

    public function getChild($root = '') {
        $data = array();
        foreach ($root as $tx => $category) {
            $rootId = $category['id'];
            $childCategories = $this->getChildCategories($rootId, $category, 1);
            $data[] = $childCategories;
        }
        return $data;
    }

    public function getChildCategories($parentId = '', &$result = '', $level = '') {

        $childData = $this->getChildCategoriesData($parentId);

    //        if($idx!=null){
    //            $result[$idx]['childs'] = $childData;
    //        }else{
    //            $result['childs'] = $childData;
    //        }
            // print_r($childData);

        if ($childData) {
            foreach ($childData as $idx => $child) {

                $childId = $child['id'];
                    // $result[]['level'] = $level+1;
                $childXs = $this->getChildCategories($childId, $child, $level + 1);
                $result['child'][] = $childXs;
            }
        }
        return $result;
    }

    public function getChildCategoriesData($parentId = '') {

      $options = array(
        'wheres'=>array('parent_id' => $parentId),
        'order'=> array('ordering' => 1)
        );
      $childData = $this->find('all',$options);

      return $childData;
    }

    public function getTreeOptions($lang = '') {
        $treeData = $this->getTreeData($lang);
        $options = array();

        foreach ($treeData as $tree) {
            $options[$tree['id']] = $tree['title'];
            if (@$tree['child']) {
                foreach ($tree['child'] as $child) {
                    $options[$child['id']] = '__' . $child['title'];
                }
            }
        }
        return $options;
    }

    public function getTreeOption($strsub = ' ', $includeLevelInKey = true) {
        $treeData = $this->getTreeData();
        $options = array();
        foreach ($treeData as $tree) {
            $this->getSubTree($tree, $options, 0, $strsub, $includeLevelInKey);
        }

        return $options;
    }

    public function getSubTree($tree = array(), &$options = array(), $level = 0, $strsub = '-', $includeLevelInKey = true) {

        $catId = $tree['c']['id'];
        $catName = str_repeat($strsub, $level) . $tree['c']['name'];
        $parentId = $tree['ch']['parent_id'];
        $ordering = $tree['ch']['ordering'];
        if ($includeLevelInKey) {
            $options[$catId . '-' . $parentId . '-' . $ordering . '-' . $level] = $catName;
        } else {
            $options[$catId . '-' . $parentId . '-' . $ordering] = $catName;
        }

        foreach ($tree as $key => $subTree) {

            if ($key !== 'c' && $key !== 'ch') {
                $this->getSubTree($subTree, $options, $level + 1, $strsub, $includeLevelInKey);
            }
        }
        return $options;
    }

    public function getCategoryByParent($parentId = '') {
        $result = array();
        $childCategories = $this->getChildCategories($parentId, $result, 1);
        return $result;
    }

}