<?php
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
class Xml implements AbstractHelper {

    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function direct() {
        
    }

    public function xml($value) {
        $response = $this->_convertValueToXml($value);
        return $response;
    }

    protected function _convertValueToXml($value) {
        $str = '';
        if (!is_array($value)) {
            $str .= $value;
        } else {
            foreach ($value as $tag => $tag_value) {
                $str .= '<' . $tag . '>';
                $str .= $this->_convertValueToXml($tag_value);
                $str .= '</' . $tag . '>';
            }
        }
        return $str;
    }

}
