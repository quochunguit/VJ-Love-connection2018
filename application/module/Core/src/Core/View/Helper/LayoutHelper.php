<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class LayoutHelper extends AbstractHelper {

    private $serviceLocator;
    private $factory;

    public function __construct($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        $this->factory = $this->getServiceLocator()->get('ServiceFactory');
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getFactory() {
        return $this->factory;
    }

    public function setFactory($factory) {
        $this->factory = $factory;
    }

    public function dateString($date, $format='d/m/Y H:i') {
        if (empty($date)) {
            return '';
        }
        if (is_object($date) && $date instanceof \MongoDate) {
            $date = $date->sec;
        } else {
            $date = strtotime($date);
        }
        return date($format, $date);
    }

    public function publishString($status, $textStatus = array('Publish', 'Unpublish')) {
        if ($status == '1') {
            return '<a class="change_status status_publish" data-action="unpublish" href="javascript:;" title="Click to ' . $textStatus[1] . '">' . $textStatus[0] . '</a>';
        } elseif ($status == '2') {
            return '<p style="color: red;">' . $textStatus[2] . '</p>';
        }
        return '<a class="change_status status_unpublish" data-action="publish" href="javascript:;" title="Click to ' . $textStatus[0] . '">' . $textStatus[1] . '</a>';
    }

    public function langString($lang) {
        if ($lang == '*') {
            $lang = 'All';
        }
        return $lang;
    }

    public function getGroupOption($selected = '') {

        $options = $this->getFactory()->getGroup();
        return $this->makeOption($selected, $options);
    }

    public function getRoleOption($selected = '') {

        $options = array("" => 'All', 'Register' => 'Register', 'Admin' => 'Admin');
        return $this->makeOption($selected, $options);
    }

    public function makeOption($selected, $options) {
        $opt_str = "";
        foreach ($options as $key => $value) {
            if ($selected == $key && strlen($selected) > 0) {
                $select = " selected ";
            } else {
                $select = "";
            }

            $opt_str .= "<option value='$key' $select >$value</option>";
        }

        return $opt_str;
    }

    public function header($field, $state, $title = '', $field_info = 'default') {

        $direction = 'asc';
        $class = "";
        $title_link = '';
        $seg = array();

        if (empty($title)) {
            $title = ucfirst($field);
            if(strpos($field, '.') !== FALSE){
                $fieldArr = explode('.', $field);
                $title = $fieldArr[1];
            }
            $title = ucwords($title);
        }

        $curr_field_order = $state->get('order.field');
        $order_dir = $state->get('order.direction');

        if (strtolower($order_dir) == 'asc') {
            $direction = 'desc';
        }

        if ($field == $curr_field_order) {
            $class = "current $order_dir";
            if ($direction == 'asc') {
                $seg[] = "<i class='fa fa-sort-desc fa-lg'></i>&nbsp;";
            } else {
                $seg[] = "<i class='fa fa-sort-up fa-lg'></i>&nbsp;";
            }
        }

        if ($direction == 'asc') {
            $title_link = "Click to sort asc";
        } else {
            $title_link = "Click to sort desc";
        }

        //fields use for export
        if ($field_info == 'default') {
            $field_info = $field;
        }
        $rel = $field_info == 'none' ? 'rel="none"' : 'rel="' . $field_info . '|' . $title . '"';
        //End fields use for export



        $seg[] = "<a href='javascript:void(0);' title='$title_link'  class='$class' $rel onclick=\"App.sort('$field','$direction')\">";
        $seg[] = $title;
        $seg[] = "</a>";

        return implode("", $seg);
    }

}
