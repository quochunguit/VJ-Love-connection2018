<?php

namespace App;

class OSHelper {

    private static function getPHPOS() {
        return strtoupper(substr(PHP_OS, 0, 3));
    }

    public static function isWin() {
        if (self::getPHPOS() == 'WIN') {
            return true;
        }
        return false;
    }

    public static function isLinux() {
        if (!self::isWin()) {
            return true;
        }
        return false;
    }

}
