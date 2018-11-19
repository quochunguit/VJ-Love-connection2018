<?php

namespace Vote\Front\Service;

class VoteService implements \Zend\EventManager\EventManagerAwareInterface {

    protected $voteMapper;
    protected $eventManager;
    
    public function __construct($voteMapper) {
        $this->voteMapper = $voteMapper;
    }

    function getVoteMapper() {
        return $this->voteMapper;
    }

    function saveInfo($data = array()) {
        $data['created'] = date('Y-m-d H:i:s');
        $data['status'] = 1;

        $data = new \ArrayObject($data);
        $vote = $this->getVoteMapper()->save($data);

        return $vote;
    }

    function isVoted($objectId, $userId, $type, $extension) {
        $objectId = intval($objectId);
        $userId = intval($userId);

        if (!$objectId || !$userId) {
            throw new \Exception('Invalid data');
        }
        $item = $this->getVoteMapper()->find(
                'first', array('wheres' => array(
                'object_id' => $objectId,
                'user_id' => $userId,
                'type' => $type,
                'extension' => $extension
            )
        ));
        if ($item) {
            return true;
        }
        return false;
    }

    public function getEventManager() {
        return $this->eventManager;
    }

    public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager) {
        $this->eventManager = $eventManager;
    }

}
