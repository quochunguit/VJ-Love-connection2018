<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Link extends AbstractHelper {

    public function postLink($label, $formAction, $confirmMessage) {
        $formName = mt_rand();
        $form = '<form action="' . $formAction . '" name="post_' . $formName . '" id="post_' . $formName . '" style="display:none;" method="post">
            <input type="hidden" name="_method" value="POST"/></form><a href="#" onclick="if (confirm(&#039; ' . $confirmMessage . ' &#039;)) { document.post_' . $formName . '.submit(); } event.returnValue = false; return false;">' . $label . '</a>';
        return $form;
    }

}

