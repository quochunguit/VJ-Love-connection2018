<?php

namespace Core\Model;

use Zend\Session\Container;

class AppState {

    protected $context = '';
    protected $_default_context = 'APP';
    protected $data = array();
    protected $session = '';

    public function __construct($context = '') {
        $this->context = $context;
        if (empty($this->context)) {
            $this->context = $this->_default_context;
        }

        $this->session = new Container($this->context);
    }

    public function getContext() {
        return $this->context;
    }

    public function setContext($context) {
        $this->context = $context;
    }

    public function set($property, $value = null) {
        if (empty($property)) {
            return false;
        }
        $this->session[$property] = $value;
    }

    public function get($property, $default = null) {
//        var_dump($property); 
//        var_dump($default);
//        print_r('----------');
        if ($this->checkPropertyState($property) !== false) {
            return $this->session[$property];
        }
        return $default;
    }

    public function checkPropertyState($property) {
        if (empty($property) || empty($this->context) || $this->session[$property]==null) {
            return false;
        } else {
            return $this->session[$property];
        }
    }

    public function header($field, $title, $curr_field_order, $order_dir) {

        $direction = 'asc';
        $class = "";
        $title_link = '';

        if (strtolower($order_dir) == 'asc') {
            $direction = 'desc';
        }

        if ($field == $curr_field_order) {
            $class = "current $order_dir";
            $title_link = "Click to sort";
        } else {
            //  $class= "current $direction";
            $title_link = "Click to sort";
        }

        $seg = array();
        $seg[] = "<a href='javascript:;' title='$title_link'  class='$class'  onclick=\"App.sort('$field','$direction')\">";
        $seg[] = $title;
        $seg[] = "</a>";

        return implode("", $seg);
    }

}
