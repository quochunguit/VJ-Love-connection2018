<?php

namespace Core\Form\Element;

use Zend\Form\Element;

class Media extends Element {

    protected $attributes = array(
        'type' => 'button',
        'class' => 'btn btn-primary show-media-popup',
    );
    public $dataMediaAppend = '';

}
