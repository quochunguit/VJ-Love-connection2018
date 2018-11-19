<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class FormMessenger extends AbstractHelper {

    function showMessage($form) {

        $messages = $form->getMessages();
        $ms = '';
        $msgWrapper = '';
        if ($messages) {

            foreach ($messages as $el => $message):
                $msg = array_values($message);
                foreach ($message as $rule => $msg):
                    $ms.= '<li>' . $msg . '</li>';

                endforeach;
            endforeach;

            if ($ms) {
                $ms = '<ul class="validator-form">' . $ms . '</ul>';

                $msgWrapper = '<div class="alert alert-warning" role="alert">';
                $msgWrapper .= '<strong>Warning!</strong>';
                $msgWrapper .= ' There are some errors in your form submission, please see below for details';
                $msgWrapper .= $ms;
                $msgWrapper .= '</div>';
            }
        }
        return $msgWrapper;
    }

}
