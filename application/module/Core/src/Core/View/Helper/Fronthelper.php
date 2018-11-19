<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Fronthelper extends AbstractHelper {

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

    public function numberFormat($number, $decimals = 0, $dec_point = ".", $thousands_sep = "."){
        $number = trim($number);
        if(is_numeric($number)){
            return number_format($number,$decimals,$dec_point,$thousands_sep);
        }

        return $number;
    }

}
