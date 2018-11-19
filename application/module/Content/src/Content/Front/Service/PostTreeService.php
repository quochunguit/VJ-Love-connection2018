<?php

namespace Content\Front\Service;

use Content\Front\Service\PostService;

class PostTreeService extends PostService {

    public function getTreeData() {
        //root
        $root = $this->getRoot();
        //second level
        $childs = $this->getChild($root);

        return $childs;
    }

    public function getRoot() {

        $options = array(
            'wheres' => array('parent_id' => '0', 'type'=>'page'),
            'order' => array('ordering' => 1)
        );
        $root = $this->getPostMapper()->find('all', $options);

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
            $childCategories = $this->getChilds($rootId, $category, 1);
            $data[] = $childCategories;
        }
        return $data;
    }

    protected function getChilds($parentId, &$result, $level) {

        $childData = $this->getChildData($parentId);


        if ($childData) {
            foreach ($childData as $idx => $child) {

                $childId = $child['id'];
                // $result[]['level'] = $level+1;
                $childXs = $this->getChilds($childId, $child, $level + 1);
                $result['child'][] = $childXs;
            }
        }
        return $result;
    }

    protected function getChildData($parentId) {

        $options = array(
            'wheres' => array('parent_id' => $parentId,'type'=>'page'),
            'order' => array('ordering' => 1)
        );
        $childData = $this->getPostMapper()->find('all', $options);

        return $childData;
    }

    public function getTreeOptions() {
        $treeData = $this->getTreeData();
        $options = array(
            0=>'-- Select --'
        );

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

    protected function getSubTree($tree, &$options, $level = 0, $strsub = '-', $includeLevelInKey = true) {

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

    public function getByParent($parentId) {
        $result = array();
        $childs = $this->getChilds($parentId, $result, 1);
        return $result;
    }

}
