<?php
namespace App;
define('SALT_LENGTH', 9);
class HashUtil {
    
    /**
     * generate password with salt given
     * 
     * @param string $plainTextPassword
     * @param string $salt
     * @return array with key: salt and hash 
     */
    public static function generateHash($plainTextPassword, $salt = null) {
        if (empty($salt)) {
            $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        } else {
            $salt = substr($salt, 0, SALT_LENGTH);
        }
        $hash = sha1($salt . $plainTextPassword);
        return array('salt' => $salt, 'hash' => $hash);
    }
    /**
     * create password with salt , salt is random string
     * 
     * @param string $plainText
     * @return string hash password
     */
    public static function createPasswordWithSalt($plainText) {
        $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        return $salt . sha1($salt . $plainText);
    }
    
    /**
     * verify password 
     * 
     * @param string $plainText
     * @param string $encPassword
     * @return boolean
     */
    public static function verifyPasswordWithSalt($plainText, $encPassword) {      
        $salt = substr($encPassword, 0, SALT_LENGTH);
        $password = substr($encPassword, SALT_LENGTH);
        $result = sha1($salt . $plainText);
        return ($result == $password);
    }
    /**
     *  create random string 
     * @return string random tring
     */
    public static function createRandomKey(){
        return md5(uniqid(rand(), true));
    }
    /**
     * generate random password
     * 
     * @param int $length length of random password
     * @return string random string
     */
    public static function randomPassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
}
    

}

