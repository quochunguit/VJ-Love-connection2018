<?php
namespace Core\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
class Lucky extends AbstractPlugin implements ServiceManagerAwareInterface {

   protected $serviceManager;

    public function randomLucky() {
        $currentHour = date("H");
        switch ($currentHour) {
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                return $this->doRandom(6, 10, 1);
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
                return $this->doRandom(11, 15, 2);
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
                return $this->doRandom(16, 20, 3);
        }
    }

    private function doRandom($fromHour, $toHour, $limit = 1) {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front'));
        if ($auth->hasIdentity()) {
            $user = $auth->getStorage()->read();
            $uid = $user['id'];
           
            //check user đã trúng giải may mắn lần nào chưa
            $luckyMapper = new App_Model_Lucky();
            $hasLuckyBefore = $luckyMapper->isLuckyBefore($uid);
          
            if (!$hasLuckyBefore) {
                //check trong khoang thoi gian nay da co nguoi trung giai chua
                $luckyIndays = $luckyMapper->isLuckyInday($fromHour, $toHour);
               
                if (count($luckyIndays) < $limit) {
                    $number = 10;
                    $sum = 0;
                    $time = time();
                    for ($i = 0; $i < 10; $i++) {
                        $value = $time % $number;
                        $sum += $value;
                        $time = floor($time / $number);
                        $number *= 10;
                    }

                    $number_random = array(17, 19, 23, 29, 31, 37, 41,40, 20, 43);
                    $index = array_rand($number_random);
                    $ran = $number_random[$index];

                    if ($sum % $ran == 0) {
                        //you are lucky
                        return $luckyMapper->save(array(
                            'user_id'=>$uid,
                            'created'=>date('Y-m-d H:i:s')
                        ));
                    }
                }
            }
        }

        return false;
    }
    public function getServiceManager() {
        return $this->serviceManager;
    }

        public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}
