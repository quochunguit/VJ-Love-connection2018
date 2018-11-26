<?php

namespace User\Front\Service;

use \Zend\ServiceManager\ServiceLocatorAwareInterface;
use \Zend\EventManager\EventManagerAwareInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use \Zend\EventManager\EventManagerInterface;

class UserLoginCookie implements ServiceLocatorAwareInterface, EventManagerAwareInterface {

    protected $userModel;
    protected $eventManager;
    protected $serviceLocator;

    public function __construct() {
        require_once VENDOR_INCLUDE_DIR.'/rc4crypt/'.'class.rc4crypt.php';

    }

    public function getCookie($cookieName) {
        $uToken = '';
        if(isset($_COOKIE[$cookieName])){
            $val = $_COOKIE[$cookieName];
            $uToken = $this->decryptCookie($val);
        }

        return $uToken;
    }

    public function setCookie($val = '',$cookieName) {
        $days = USER_LOGIN_COOKIE_DAYS;
        $expire = time() + ($days * 24 * 60 * 60);
        $value = $val ? $this->encryptCookie($val) : $val;
        setcookie($cookieName, $value, $expire, '/');
    }

    public function removeCookie($name = USER_LOGIN_COOKIE_NAME){
        unset($_COOKIE[$name]);
        setcookie($name, null, -1, '/');
    }

    // Encrypt cookie
    public function encryptCookie( $value ) {
        $key = USER_LOGIN_COOKIE_HASH_KEY;
        $newValue = \rc4crypt::encrypt(md5($key) , $value);
        //$newValue = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $value, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
        return( $newValue );
    }

    // Decrypt cookie
    public function decryptCookie( $value ) {
        $key = USER_LOGIN_COOKIE_HASH_KEY;
        $newValue = \rc4crypt::decrypt(md5($key), $value);
        //$newValue = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $value ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0");
        return( $newValue );
    }

    public function getUserModel() {
        if(!$this->userModel){
            $this->userModel = $this->getServiceLocator()->get('User\Front\Model\User');
        }
        return $this->userModel;
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}