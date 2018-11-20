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
                $this->setLangCode($code);
            }
            $_SESSION['closePop'] = true;
        }
        //--End set lang
        if($urlRedirect){
            $this->redirectToUrl($urlRedirect);
        }else{
            $codeShort = $this->getLangCode(true);
            $this->redirectToRoute('home', array('lang'=>$codeShort));
        }
    }

}
