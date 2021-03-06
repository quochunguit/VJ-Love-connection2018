<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class IndexController extends FrontController{
  
    public function indexAction(){

      $this->setMetaData(array(), '');
      $this->layout()->setVariables(array('parent_page'=>'home', 'page'=>'home'));

      $curLang = $this->getLangCode(true);

      $factory = $this->getFactory();
      $login = $this->getUserLogin();
      $logout = '';

      if($login){
        $logout = '<button id="site-logout" onclick="App.Site.userLogout('.$login->id.');">Logout</button>';
      }

        $params = $this->getParams();
        $_SESSION['show-fg']= false;
        if($params['fpc']){
          $userModel = $this->getUserModel();
          $user = $userModel->getItem(array('forget_pass_code'=>$params['fpc']));
          if($user){
            $_SESSION['show-fg']= $params['fpc'];
          }
        }
        $modelBestContest = $this->getContestModel();
        $modelBestContest->setLimit(10);
        $modelBestContest->setState('order.field', 'votes');
        $bestContest = $modelBestContest->getItems();
        $bestContest = $bestContest->toArray();

        $modelNewestContest = $this->getContestModel();
        $modelNewestContest->setParams($params);
        $modelNewestContest->setLimit(10);
        $modelNewestContest->setState('order.field', 'created');
        $newestContest = $modelNewestContest->getItems();
        $newestContest = $newestContest->toArray();
        $this->setMetaData(array(), $this->translate('Slogan'));

      return new ViewModel(array(
          'newestContest'=> $newestContest,
          'bestContest' => $bestContest

          //'paging' => $modelNewestContest->getPaging()
      ));
    }
}
