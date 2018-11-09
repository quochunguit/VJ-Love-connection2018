<?php

namespace Category\Front\Block\Category;

use Block\Front\Block\Type\AbstractType;
use Block\Front\Block\Type\ParamsInterface;

class Category extends AbstractType{

    public function getDefaultParams() {
        return array('template' => 'block/category/default');
    }

    public function getData() {

        $categoryService = $this->getServiceLocator()->get('CategoryService');
        $tree = $categoryService->getTreeData();
        return $tree;
    }
}
