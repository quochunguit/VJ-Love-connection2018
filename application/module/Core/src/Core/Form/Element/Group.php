<?php

namespace Core\Form\Element;

use Zend\Form\Element\Select;

class Group extends Select {

    protected $serviceLocator;

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setOptions($options) {
        parent::setOptions($options);
        if (array_key_exists('serviceLocator', $options)) {
            $this->serviceLocator = $options['serviceLocator'];

            $groupModel = $this->getServiceLocator()->get('User\Admin\Model\Group');

            $items = $groupModel->getAllItemsToKeyVal(array(), array('key' => 'id', 'value' => 'title'));
            unset($items[9]);

            $this->setValueOptions($items);
        }
    }

}

?>
