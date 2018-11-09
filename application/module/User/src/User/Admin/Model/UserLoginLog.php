<?php

namespace User\Admin\Model;

use Core\Model\AppModel;
use Zend\Db\Sql\Select;

class UserLoginLog extends AppModel {

    public $table = 'bz1_users_login_log';
    public $context = 'users_login_log';

    public function getLoginFail($ip = '', $dateTime = '', $type = 'fail', $limit = 0) {
        $dateTime = $dateTime ? $dateTime : date('Y-m-d H');

        $select = new Select($this->table);
        $select->where(array('status' => 1));
        $select->where(array("DATE_FORMAT(created,'%Y-%m-%d %H') = '".$dateTime."'", 'ip' => $ip, 'type' => $type));

        $select->order($this->table . ".created desc");
        if ($limit > 0) {
            $select->limit($limit);
        }

        $result = $this->selectWith($select);
        if ($result) {
            $items = $result->toArray();
            return $limit == 1 ? $items[0] : $items;
        }

        return array();
    }

}
