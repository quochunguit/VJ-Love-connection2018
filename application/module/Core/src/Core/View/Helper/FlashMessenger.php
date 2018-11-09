<?php
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;

class FlashMessenger extends AbstractHelper{

    protected $locator;
    public function __construct($locator) {
        $this->locator = $locator;
    }

    public function __invoke() {
        $serviceLocator = $this->locator;
        $plugin = $serviceLocator->get('ControllerPluginManager');
        $flashMessenger = $plugin->get('flashmessenger');
        
        $messages = $flashMessenger->getMessages();
        if ($flashMessenger->hasCurrentMessages()) { // Check for any recently added messages
            $messages += $flashMessenger->getCurrentMessages();
            $flashMessenger->clearCurrentMessages();
        }

        $msg = '';
        if($messages){
            $msg = '<div class="alert alert-success" role="alert">';
            $msg .= '<strong>Message: </strong>';
            
            foreach($messages as $message){
              $msg.='<span class="alert-row">'.$message.'</span>';
            }

            $msg .= '</div>';
        }  

        return $msg;
    }
}