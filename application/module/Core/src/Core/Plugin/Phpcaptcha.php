<?php

namespace Core\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

require_once VENDOR_INCLUDE_DIR . '/phpcaptcha/php-captcha.inc.php';

class Phpcaptcha extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $area = 'front';
    protected $serviceManager;

    function image() {

        $captchaPath = VENDOR_INCLUDE_DIR . '/phpcaptcha';

        $aFonts = array(
            $captchaPath . '/fonts/VeraBd.ttf',
            $captchaPath . '/fonts/VeraIt.ttf',
            $captchaPath . '/fonts/Vera.ttf'
        );
        //print_r($aFonts);
        $oVisualCaptcha = new \PhpCaptcha($aFonts, 126, 38);
        $oVisualCaptcha->SetNumChars(5);
        $imgArr = array(
            'bg1' => 'BG1',
            'bg2' => 'BG2',
            'bg3' => 'BG3',
            'bg4' => 'BG4',
            'bg5' => 'BG5'
        );
        $bgRand = array_rand($imgArr, 1);
        $oVisualCaptcha->SetBackgroundImages($captchaPath . '/images/' . $bgRand . '.jpg');
        $oVisualCaptcha->Create();
    }

    function check($userCode, $caseInsensitive = true) {
        if ($caseInsensitive) {
            $userCode = strtoupper($userCode);
        }

        if (!empty($_SESSION[CAPTCHA_SESSION_ID]) && $userCode == $_SESSION[CAPTCHA_SESSION_ID]) {
            unset($_SESSION[CAPTCHA_SESSION_ID]); // clear to prevent re-use
            return true;
        }
        return false;
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}
