<?php

namespace Metatag\Admin\Service;

class MetatagService implements \Zend\EventManager\EventManagerAwareInterface {

    protected $metatagMapper;
    protected $eventManager;

    public function __construct($metatagMapper) {
        $this->metatagMapper = $metatagMapper;
    }

    function getMetatagMapper() {
        return $this->metatagMapper;
    }

    function saveMetatag($params) {

        $objectId = intval($params['id']);
        $objectType = $params['type'];
        $metaTitle = $params['meta_title'];
        $metaDescription = $params['meta_description'];
        $metaKeywords = $params['meta_keywords'];

        if (!$objectId) {
            throw new \Exception('Invalid data');
        }

        $this->delete($objectId, $objectType);

        $data = array(
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'object_id' => $objectId,
            'extension' => $objectType,
        );
        $data = new \ArrayObject($data);
        $metatag = $this->getMetatagMapper()->save($data);

        return $metatag;
    }

    function getMetatags($id, $type) {

        $id = intval($id);


        if (!$id) {
            throw new \Exception('Invalid data');
        }
        $items = $this->getMetatagMapper()->find(
                'first', array(
            'wheres' => array(
                'object_id' => $id,
                'extension' => $type,
            )
        ));

        return $items;
    }

    function delete($objectId, $objectType) {
        $this->getMetatagMapper()->delete(array('object_id' => $objectId, 'extension' => $objectType));
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

}
