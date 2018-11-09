<?php

namespace Core\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class PhpThumb extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $_phpThumb;
    protected $_org;
    protected $serviceManager;

    function __construct($org) {
        include_once VENDOR_INCLUDE_DIR . '/phpthumb/ThumbLib.inc.php';
        $this->_org = $org;
        $this->_phpThumb = \PhpThumbFactory::create($org);
    }

    public function thumb($org, $des, $width, $height) {
        try {
            $this->_phpThumb->resize($width, $height);
            $this->_phpThumb->save($des);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function crop($x, $y, $xCrop, $yCrop) {
        try {
            $this->_phpThumb->crop($x, $y, $xCrop, $yCrop);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function cropFromCenter($x, $y) {
        try {
            $this->_phpThumb->cropFromCenter($x, $y);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function cropImageCenter($folderContainImageSave, $size_desire) {
        try {
            $size = explode('x', trim($size_desire));

            $imageinfo = getimagesize($this->_org);
            $array_info = explode('"', $imageinfo[3]);
            $width = $array_info[1];
            $height = $array_info[3];

            if ($width > $height) {
                $this->_phpThumb->cropFromCenter($height);
            }
            if ($width < $height) {
                $this->_phpThumb->cropFromCenter($width);
            }
            $this->_phpThumb->save($folderContainImageSave);
            $new_thumb = \PhpThumbFactory::create($folderContainImageSave);
            $new_thumb->resize($size[0], $size[1]);
            $new_thumb->save($folderContainImageSave);
            return true;
        } catch (Zend_Mail_Exception $e) {
            return false;
        }
    }

    public function resize($width, $height) {
        try {
            $this->_phpThumb->resize($width, $height);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function save($fileName) {
        $this->_phpThumb->save($fileName, $format = null);
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}
