<?php

namespace App;

require_once VENDOR_DIR . DS.'phpthumb'.DS.'ThumbLib.inc.php';

class ImageHelper {

    public static function thumb($org, $dest, $width, $height) {
        try {
            if (!file_exists($org)) {
                throw new \Exception('File is not exists.');
            }
            $phpThumb = \PhpThumbFactory::create($org);

            $phpThumb->resize($width, $height);
            $phpThumb->save($dest);
            
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
        if (!file_exists($dest)) {
            return false;
        }
        return true;
    }

}
