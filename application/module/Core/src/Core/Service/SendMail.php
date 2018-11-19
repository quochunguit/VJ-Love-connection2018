<?php
namespace Core\Service;

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
//require_once VENDOR_INCLUDE_DIR . '/mailgun-php-master/vendor/autoload.php'; /*At folder "mailgun-php-master" need run: composer update*/
use Mailgun\Mailgun;

class SendMail {

    private $serviceLocator;

    public function __construct($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    /*Send email by ZendMail*/
    function send($info = array(), $emailsCC = array()) {
         try {

            $template = $info['template'];
            $subject = $info['subject'];
            $fromName = $info['from']['name'];
            $fromEmail = $info['from']['email'];

            $toName = $info['to']['name'];
            $toEmail = $info['to']['email'];
            $data = $info['data'];

            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
            $htmlBody = $this->renderer->render($template, $data);

            $htmlPart = new MimePart($htmlBody);
            $htmlPart->charset = 'utf-8';
            $htmlPart->type = "text/html";
            $textBody = '';
            $textPart = new MimePart($textBody);
            $textPart->type = "text/plain";

            $body = new MimeMessage();
            $body->setParts(array($textPart,$htmlPart));

            $message = new Mail\Message();
            $message->setFrom($fromEmail, $fromName);
            $message->addTo($toEmail, $toName);
            if($emailsCC){
                foreach ($emailsCC as $value) {
                    $message->addCc($value);
                }  
            }

            $message->setSubject($subject);

            $message->setEncoding("UTF-8");
            $message->setBody($body);
            $message->getHeaders()->get('content-type')->setType('multipart/alternative');
            $transport = $this->getServiceLocator()->get('MailTransport');


            $transport->send($message);

        } catch(\Zend\Mail\Exception $e) {
             echo $e->getMessage();die;
             return -1;
        }
        catch(\Exception $ex) {
            echo $ex->getMessage();die;
            return -1;
        }     
    }

    function sendAttach($info = array(), $emailsCC = array()) {
        try {
            $template = $info['template'];
            $subject = $info['subject'];

            $fromName = $info['from']['name'];
            $fromEmail = $info['from']['email'];

            $toName = $info['to']['name'];
            $toEmail = $info['to']['email'];
            
            $attachName = $info['attach']['file_name'];
            $attachPath = $info['attach']['file_path'];
            $attachType = $info['attach']['file_type'];

            $data = $info['data'];

            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
            $htmlBody = $this->renderer->render($template, $data);  
        
            $htmlPart = new MimePart($htmlBody);
            $htmlPart->charset = 'utf-8';
            $htmlPart->type = "text/html";
            $textBody = '';
            $textPart = new MimePart($textBody);
            $textPart->type = "text/plain";

            if($attachName){ //Attachment file
                $fileContent = file_get_contents($attachPath);
                $contentAttachment = new \Zend\Mime\Part($fileContent);
                $contentAttachment->type         = $attachType ? $attachType : Mime::TYPE_OCTETSTREAM;
                $contentAttachment->disposition  = Mime::DISPOSITION_ATTACHMENT;
                $contentAttachment->encoding     = Mime::ENCODING_BASE64;
                $contentAttachment->filename     = $attachName;
            }

            $body = new MimeMessage();
            $body->setParts(array($textPart, $contentAttachment, $htmlPart));

            $message = new Mail\Message();
            $message->setFrom($fromEmail, $fromName);
            $message->addTo($toEmail, $toName);
            if($emailsCC){
                foreach ($emailsCC as $value) {
                    $message->addCc($value);
                }  
            }
           
            $message->setSubject($subject);

            $message->setEncoding("UTF-8");
            $message->setBody($body);
            $message->getHeaders()->get('content-type')->setType('multipart/alternative');

            $transport = $this->getServiceLocator()->get('MailTransport');

            $transport->send($message);
        } catch(\Zend\Mail\Exception $e) {
            return -1;
        }
        catch(\Exception $ex) {
            return -1;
        } 
    }
    /*End Send email by ZendMail*/
    /*=============================================================================================*/
    /*Send email by MailGun
    * 1. Login on https://mailgun.com
    * 2. Menu Domain: add your domain (ex: your sender mail my@abc.com --->domain abc.com must to active by MailGun)
    * 3. Menu Campaign: Sent email to user (tracking opened, clicked,....)

    * 4. Code Demo In Controller
        $mail = $this->getServiceLocator()->get('SendMail');
        $result = $mail->sendMailGun(array(
                    'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                    'to' => array('name' => 'BzTest', 'email' => 'nhanptit90@gmail.com'),
                    'subject' => 'Subject test',
                    'template' => 'email/clcp_promotion',
                    'campaign-id' =>'w7rb1',
                    'api-key'=> 'key-c75f672c7092a1e23820e3d560d3c10b',
                    'domain'=>'ensurevietnam.com',
                    'data' => array()
                ));
        print_r($result); die;
    */
    function sendMailGun($info = array(), $emailsCC = array()) {
        try {
            $campaignId = $info['campaign-id'];
            $apiKey = $info['api-key'];
            $domain = $info['domain'];

            $template = $info['template'];
            $subject = $info['subject'];
            $fromName = $info['from']['name'];
            $fromEmail = $info['from']['email'];

            $toName = $info['to']['name'];
            $toEmail = $info['to']['email'];
            $data = $info['data'];

            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
            $htmlBody = $this->renderer->render($template, $data);  
        
            # Now, compose and send your message.
            # https://documentation.mailgun.com/api-sending.html#sending
            $arrInfoMailGun = array(
                                'from'  => $fromEmail, 
                                'to'    => $toEmail, 
                                'subject'   => $subject, 
                                'html'  => $htmlBody,
                            );
            if($campaignId){
                $arrInfoMailGun['o:campaign'] = $campaignId;
            }

            $mg = new Mailgun($apiKey);
            return $mg->sendMessage($domain, $arrInfoMailGun);

        } catch(\Zend\Mail\Exception $e) {
            return -1;
        }
        catch(\Exception $ex) {
            return -1;
        }     
    }
    /*End Send email by MailGun*/

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
