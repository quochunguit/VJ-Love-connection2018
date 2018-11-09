<?php

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormInput;

class FormInputCustom extends FormInput {

    protected function prepareAttributes(array $attributes) {

        foreach ($attributes as $key => $value) {
            $attribute = strtolower($key);

            // Normalize attribute key, if needed
            if ($attribute != $key) {
                unset($attributes[$key]);
                $attributes[$attribute] = $value;
            }

            // Normalize boolean attribute values
            if (isset($this->booleanAttributes[$attribute])) {
                $attributes[$attribute] = $this->prepareBooleanAttributeValue($attribute, $value);
            }
        }

        return $attributes;
    }

}
