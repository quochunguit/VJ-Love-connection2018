<?php

namespace Category\Front\Factory\Service;
use \Zend\ServiceManager\FactoryInterface;
use Category\Front\Service\CategoryService;
use \Zend\ServiceManager\ServiceLocatorInterface;

class CategoryServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {

        $categoryService = new CategoryService();

        return $categoryService;
    }

}
