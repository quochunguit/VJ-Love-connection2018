<?php

namespace Language\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class LanguageController extends FrontController {

    public function switchedAction() {
        $params = $this->getParams();
        $code = $params['code'];
        $urlRedirect = $params['url_redirect'];
        //--Set lang
        if($code){
            $langModel = $this->getLangModel();
            $curLang = $langModel->getItem(array('lang_code'=>$code));
            if($curLang['status'] == 1){
                $_SESSION['closePop'] = true;
                $this->setLangCode($code);
            }

            $userLoginCookieService = $this->getServiceLocator()->get('User\Front\Service\UserLoginCookie');
            $userLoginCookieService->setCookie($code,'LanguageCookie');
        }
        //--End set lang
        if($urlRedirect){
           // echo $urlRedirect;die;
            $len = strlen($urlRedirect);
            $tmpUrl = substr($urlRedirect,3,$len);
            $curLang = $this->getLangCode(true);
            $urlRedirect = '/'.$curLang.$tmpUrl;
            $this->redirectToUrl($urlRedirect);

        }else{
            $codeShort = $this->getLangCode(true);
            $this->redirectToRoute('home', array('lang'=>$codeShort));
        }
    }

}
