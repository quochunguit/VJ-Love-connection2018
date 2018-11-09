<?php

namespace Category\Front\Service;

class CategoryService implements \Zend\EventManager\EventManagerAwareInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    protected $serviceLocator;
    protected $eventManager;

    public function getTreeData() {
        //root
        $root = $this->getRoot();
        //second level
        $childs = $this->getChild($root);

        return $childs;
    }

    public function getRoot() {

        $options = array(
            'wheres' => array('parent_id' => '0'),
            'order' => array('ordering' => 1)
        );
        $root = $this->getCategoryMapper()->find('all', $options);

        return $root;
    }

    public function getRootOptions() {
        $root = $this->getRoot();

        $options = array();
        foreach ($root as $key => $cat) {
            $options[$cat['id']] = $cat['title'];
        }

        return $options;
    }

    public function getChild($root) {
        $data = array();
        foreach ($root as $tx => $category) {
            $rootId = $category['id'];
            $childCategories = $this->getChildCategories($rootId, $category, 1);
            $data[] = $childCategories;
        }
        return $data;
    }

    public function getChildCategories($parentId, &$result, $level) {

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

    public function getChildCategoriesData($parentId) {

        $options = array(
            'wheres' => array('parent_id' => $parentId),
            'order' => array('ordering' => 1)
        );
        $childData = $this->getCategoryMapper()->find('all', $options);

        return $childData;
    }

    public function getTreeOptions() {
        $treeData = $this->getTreeData();
        $options = array();

        foreach ($treeData as $tree) {
            $options[$tree['id']] = $tree['title'];
            if ($tree['child']) {
                foreach (@$tree['child'] as $child) {
                    $options[$child['id']] = ' ' . $child['title'];
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

    public function getSubTree($tree, &$options, $level = 0, $strsub = '-', $includeLevelInKey = true) {

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

    public function getCategoryByParent($parentId) {
        $result = array();
        $childCategories = $this->getChildCategories($parentId, $result, 1);
        return $result;
    }

    function getCategoryMapper() {
        return $this->getServiceLocator()->get('Category\Front\Model\Category');
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
