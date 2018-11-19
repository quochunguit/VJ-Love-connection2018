<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormInput;

class FormMedia extends FormInput {

    public function render(\Zend\Form\ElementInterface $element) {

        $attributes = $element->getAttributes();

        $value = $element->getValue();

        $labelButton = $attributes['data-label'];
        $element->setValue($labelButton);



        $input = parent::render($element);

        $extra = '<div class="files %s"></div>
                 <div class="clear">&nbsp;</div>';


        $attrMediaAppend = $attributes['data-media-append'];
        $attrMediaAppend = $attrMediaAppend ? $attrMediaAppend : 'file1';
        $extraHtml = sprintf($extra, $attrMediaAppend);

        if (is_string($value) && !empty($value)) {
            $value = array($value);
        }
        if (!$value) {
            $value = array();
        }
        $value = json_encode($value);
        $script = "<script type='text/javascript'>var $attrMediaAppend = $value;</script>";

        return $input . $extraHtml . $script;
    }

}
