<?php

namespace User\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;


class UserGroup extends AppModel {

    public $table = 'bz1_users_groups';
    public $context = 'user_group';

    
    public function getGroupOfUser($id = ''){
        $where = array('user_id'=>$id);
        return $this->getAllItems($where);
    }
    

    

}