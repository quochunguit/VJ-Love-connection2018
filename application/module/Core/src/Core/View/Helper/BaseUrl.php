<?php

//$this->baseUrl() in view file
class App_View_Helper_BaseUrl extends Zend_View_Helper_Abstract {
    public function baseUrl() {
        return Zend_Controller_Front::getInstance()->getBaseUrl();
    }
}