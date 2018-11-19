<?php

include_once dirname(dirname(dirname(__FILE__))) . '/vendor_include/phpthumb/ThumbLib.inc.php';

class Cropimage {

    var $x = 0;
    var $y = 0;
    var $w = 0;
    var $h = 0;
    //full path to image file
    var $imgFile;
    var $newImgFile;
    var $pathToImgCrop;
    var $pathToImgAfterCrop;

    public function __construct($x = 0, $y = 0, $w = 0, $h = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
    }

    function crop() {
        if(file_exists($this->pathToImgCrop . '/' . $this->imgFile)){
            $thumb = \PhpThumbFactory::create($this->pathToImgCrop . '/' . $this->imgFile);
            $thumb->crop($this->x, $this->y, $this->w, $this->h);
            $thumb->save($this->pathToImgAfterCrop . '/' . $this->newImgFile);
        }  
    }

    public function getX() {
        return $this->x;
    }

    public function setX($x) {
        $this->x = $x;
    }

    public function getY() {
        return $this->y;
    }

    public function setY($y) {
        $this->y = $y;
    }

    public function getW() {
        return $this->w;
    }

    public function setW($w) {
        $this->w = $w;
    }

    public function getH() {
        return $this->h;
    }

    public function setH($h) {
        $this->h = $h;
    }

    public function getImgFile() {
        return $this->imgFile;
    }

    public function setImgFile($imgFile) {
        $this->imgFile = $imgFile;
    }

    public function getNewImgFile() {
        return $this->newImgFile;
    }

    public function setNewImgFile($newImgFile) {
        $this->newImgFile = $newImgFile;
    }

    public function getPathToImgCrop() {
        return $this->pathToImgCrop;
    }

    public function setPathToImgCrop($pathToImgCrop) {
        $this->pathToImgCrop = $pathToImgCrop;
    }

    public function getPathToImgAfterCrop() {
        return $this->pathToImgAfterCrop;
    }

    public function setPathToImgAfterCrop($pathToImgAfterCrop) {
        $this->pathToImgAfterCrop = $pathToImgAfterCrop;
    }

}
