<?php

namespace Core\InputFilter;
/*require_once VENDOR_DIR . '/htmlpurifier/HTMLPurifier.auto.php'; //TODO:Dev vendor_php_5_3_3*/
class MyHtmlPurifier {

    static $instance;
    private $htmlpurifier;

    public function __construct() {
        $this->initFilter();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initFilter() {

        if (!$this->htmlpurifier) {
            \HTMLPurifier_Bootstrap::registerAutoload();
            $config = \HTMLPurifier_Config::createDefault();

            if (!is_dir(WEB_ROOT . '/var/cache/htmlpurifier')) {
                @mkdir(WEB_ROOT . '/var/cache/htmlpurifier', 0775, true);
            }

            $config->set('Cache.SerializerPath', WEB_ROOT . '/var/cache/htmlpurifier/');
            $config->set('Attr.EnableID', true);
            $config->set('HTML.TargetBlank',true);
            $config->set('HTML.Strict', true);

            $this->htmlpurifier = new \HTMLPurifier($config);
        }
    }

    public function plaintext($string) {

        $data = $this->htmlpurifier->purify($string);
        return $data;
    }

    public function purify($string, $config = array()) {

        $data = $this->htmlpurifier->purify($string);
        return $data;
    }

    public function autoCleanPost($data = array()) {
        $data = $_POST;
        return $this->autoClean($data);
    }

    public function autoCleanGet() {
        $data = $_GET;
        return $this->autoClean($data);
    }

    public function autoClean($data) {
       
        if(!$data){
            return array();
        }
        foreach ($data as $key => $val) {
            if (!is_array($val)) {
                $val = $this->purify($val);
                $data[$key] = $val;
            } else {
                foreach ($val as $k1 => $v1) {
                    if (!is_array($v1)) {
                        $v1 = $this->purify($v1);
                        $data[$key][$k1] = $v1;
                    }
                }
            }
        }
        return $data;
    }

}


