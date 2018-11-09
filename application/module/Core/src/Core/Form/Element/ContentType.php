<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class ContentType extends Select {

    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];
            $model = $this->getServiceLocator()->get('Contenttype\Admin\Model\Contenttype');
            $items = $model->getAllItemsToKeyVal(array(), array('key' => 'type', 'value' => 'title'));
            $this->setValueOptions($items);
        }
    }

}

?>
