<?php
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

//$this->baseUrl() in view file
class Facebook extends AbstractHelper {
    public function direct() {
        parent::direct();
    }
    public function like() {
        return '';
    }
    public function comment(){
        
    }
}