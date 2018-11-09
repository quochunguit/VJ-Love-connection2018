<?php

namespace App;

class UserAgentHelper {

    const FIREFOX = 'Firefox';
    const MSIE = 'MSIE';
    const SAFARY = 'Safary';
    const OPERA = 'Opera';

    public static function getAgentInfo($browser) {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $agent = substr($agent, $browser);
        return strtoupper($agent);
    }

    public static function getBrowser() {
        if (self::getAgentInfo(self::FIREFOX)) {
            return self::FIREFOX;
        } else if (self::getAgentInfo(self::OPERA)) {
            return self::OPERA;
        } else if (self::getAgentInfo(self::MSIE)) {
            return self::MSIE;
        } else if (self::getAgentInfo(self::SAFARY)) {
            return self::SAFARY;
        }
    }

}
