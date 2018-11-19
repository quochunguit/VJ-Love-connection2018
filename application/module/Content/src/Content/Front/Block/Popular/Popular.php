<?php

namespace Category\Front\Block\Category;

use Block\Front\Block\Type\AbstractType;

class Category extends AbstractType {

    public function getDefaultParams() {
        return array('template' => 'block/category/default');
    }

    public function getBlockData() {
        
        $categoryService = $this->getServiceLocator()->get('CategoryService');
        $tree = $categoryService->getTreeData();
        return $tree;
        
        
    }

}
