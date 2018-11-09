<?php
namespace Core\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Sms extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $serviceManager;

    /*----- VietGuy Send SMS---------*/
    /*
        Info array is info_sms_config of VietGuy API and content
        Ex:
        $infoArray = array(
            "u" => "EssanceVN",
            "pwd" => "xxxxx",
            "from" => "EssanceVN",
            "phone" => $phone,
            "sms" => $sms
        );
    */
    public function vietGuySms($phone, $sms){
        //echo 'aaaa';die;
        if($phone && $sms){
            $infoArray = array(
                            "u" => USERNAME,
                            "pwd" => PASSCODE,
                            "from" => SENDER, //1900xxxx
                            "phone" => $phone,
                            "sms" => $sms
                        );

            return $this->postCurl('https://cloudsms.vietguys.biz:4438/api/index.php', $infoArray, BASE_URL);
        }
    }

    private function postCurl($url, $pvars, $referer, $timeout = 30){
        $curl = curl_init();
        $post = http_build_query($pvars);
        if (isset($referer)) {
            curl_setopt($curl, CURLOPT_REFERER, $referer);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0", rand(4, 5)));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
        $html = curl_exec($curl);
        curl_close($curl);
        return $html;
    }
    /*-----End VietGuy Send SMS---------*/

    /*----- Nusoap SMS ----- */
    function nusoapSms($phone = '', $message = '') {
        $resultInfo = array();
        if ($phone != '' && $message != '') {
            require_once VENDOR_INCLUDE_DIR . '/nusoap/nusoap.php';

            $soapWSDL = 'http://210.211.109.118/apibrandname/send?wsdl';
            $soapUserName = 'abbottensure';
            $soapPass = '';
            $soapBrandName = 'BzCMSCustom';

            try {
                $proxyhost = '';
                $proxyport = '';
                $proxyusername = '';
                $proxypassword = '';
                $client = new \nusoap_client($soapWSDL, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword, 20, 20);
            } catch (Exception $ex) {
                array_push($this->exeption, $ex);
            }

            try {
                $result = $client->call("send", array(
                    "USERNAME" => $soapUserName,
                    "PASSWORD" => $soapPass,
                    "BRANDNAME" => $soapBrandName,
                    'TYPE'=>1,
                    "PHONE" => $phone,
                    "MESSAGE" => $message 
                ));
                //print_r($result); die;

                if (array_key_exists('return', $result)) {
                    $code = $result['result'];
                    if ($code == 0) {
                        $resultInfo = array('status' => true, 'message' => 'Gửi tin nhắn thành công!');
                    } else {
                        $resultInfo = array('status' => false, 'message' => 'Gửi tin nhắn không thành công!');
                    }
                } else {
                    $resultInfo = array('status' => false, 'message' => 'Đã xảy ra lỗi!');
                }
            } catch (Exception $ex) {
                $resultInfo = array('status' => false, 'message' => 'Đã xảy ra lỗi!');
            }
        } else {
            $resultInfo = array('status' => false, 'message' => 'Vui lòng truyền tham số hợp lệ!');
        }

        return $resultInfo;
    }

    /*----- End Nusoap SMS ----- */



    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}