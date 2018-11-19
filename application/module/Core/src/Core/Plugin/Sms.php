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

            $soapWSDL = VJ_WSDL_DEFINE;
            $soapUserName = VJ_SOAP_USERNAME;
            $soapPass = VJ_SOAP_PASSWORD;
            $soapBrandName = '';

            try {
                $proxyhost = '';
                $proxyport = '';
                $proxyusername = '';
                $proxypassword = '';
                $client = new \nusoap_client($soapWSDL, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword, 20, 20);
            } catch (Exception $ex) {
                array_push($this->exeption, $ex);
                //print_r($ex);die;
            }
            //print_r($client);die;

            try {
                $client = new \SoapClient(VJ_WSDL_DEFINE);
                $result = $client->__soapCall("SentSMSDOM", array('request'=>array('ClientCredential'=>array("Username" => $soapUserName,
                    "Password" => $soapPass),
                    "phonenumber" => $phone,
                    "content" => $message
                )));
                print_r($result);die;

                if (array_key_exists('return', $result)) {
                    echo 'aaaaaa';die;
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
                echo $ex->getMessage();die;
                $resultInfo = array('status' => false, 'message' => 'Đã xảy ra lỗi!');
            }
        } else {
            $resultInfo = array('status' => false, 'message' => 'Vui lòng truyền tham số hợp lệ!');
        }

        return $resultInfo;
    }

    /*----- End Nusoap SMS ----- */

    /*----- SOAP SMS ----- */

    public function sendSoap($phone = '', $message = '') {
        $checkVnPhone = $this->checkVnPhone($phone);
        if($checkVnPhone){
            $remoteFunction = 'SentSMSDOM';
        }else{
            $remoteFunction = 'SentSMSITL';
        }
        $options = array(
            'cache_wsdl' => 0,
            'trace' => 1,
            'stream_context' => stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            )));
        $soapClient = new \SoapClient(VJ_WSDL_DEFINE,$options);

        // Setup the RemoteFunction parameters
        $ap_param = array('request'=>array('ClientCredential'=>array('Username'=> VJ_SOAP_USERNAME,
            'Password'    =>    VJ_SOAP_PASSWORD),
            'phonenumber'     =>    $phone,'content'=>$message));

        // Call RemoteFunction ()
        $error = 0;
        try {
            $result = $soapClient->__call($remoteFunction, array($ap_param));
            $result = (array)$result;

            if($result[$remoteFunction.'Result']->_Success==1){
                $resultInfo = array('status' => true, 'message' => 'Send sms Success!');
            }else{
                $resultInfo = array('status' => false, 'message' => 'Send sms Failed!');
            }
            return $resultInfo;
        } catch (SoapFault $fault) {
            $error = 1;
            print("
            alert('Sorry, blah returned the following ERROR: ".$fault->faultcode."-".$fault->faultstring.". We will now take you back to our home page.');
            window.location = 'main.php';
            ");
        }


    }

    private function checkVnPhone($phone){
        if(substr($phone,0,2)== '84'){
            return true;
        }else{
            return false;
        }
    }



    /*----- End SOAP SMS ----- */



    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}