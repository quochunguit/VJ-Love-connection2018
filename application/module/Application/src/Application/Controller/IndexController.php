<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class IndexController extends FrontController{
  
    public function indexAction(){
      $this->setMetaData(array(), '');
      $this->layout()->setVariables(array('parent_page'=>'home', 'page'=>'home'));

      $curLang = $this->getLangCode();

      $factory = $this->getFactory();
      $login = $this->getUserLogin();
      $logout = '';

      if($login){
        $logout = '<button id="site-logout" onclick="App.Site.userLogout('.$login->id.');">Logout</button>';
      }

      /*get contests with params*/
        $modelBestContest = $this->getContestModel();
        $modelBestContest->setLimit(3);
        $modelBestContest->setState('order.field', 'votes');
        $bestContest = $modelBestContest->getItems();
        $bestContest = $bestContest->toArray();

        $params = $this->getParams();
        $modelNewestContest = $this->getContestModel();
        $modelNewestContest->setParams($params);
        $modelNewestContest->setLimit(9);
        $modelNewestContest->setState('order.field', 'created');
        $newestContest = $modelNewestContest->getItems();
        $newestContest = $newestContest->toArray();

      return new ViewModel(array(
        'bestContest'=>$bestContest,
          'newestContest'=>$newestContest,
          //'paging' => $modelNewestContest->getPaging()
      ));
    }
}
