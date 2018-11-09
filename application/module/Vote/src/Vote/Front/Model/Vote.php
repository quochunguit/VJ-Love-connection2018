<?php

namespace Vote\Front\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;

class Vote extends AppModel {

    public $table = 'bz1_votes';
    public $context = 'vote_front';

  	public function getCountDataVote($objectId, $type = 'votes', $extension = 'contest') {
        switch ($type) {
            case 'votes': //Votes
                $sql = "select count(v.id) as count from ".$this->table." as v join bz1_users as u on u.id = v.user_id where v.object_id = '".$objectId."' and v.type='".$type."' and v.extension='".$extension."'";
                break;
            default: //Shares
                $sql = "select count(v.id) as count from ".$this->table." as v where v.object_id = '".$objectId."' and v.type='".$type."' and v.extension='".$extension."'";
                break;
        }
        //echo $sql; die;

        $sm = $this->getServiceLocator();
        $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        $statement = $this->adapter->query($sql);
        $results = $statement->execute();
        $rows = $results->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        
        return $rows[0]['count'];
    }
}
