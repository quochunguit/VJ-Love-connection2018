<?php

namespace Language\Front\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Core\Model\AppModel;
use Zend\Db\Sql\Select;

class Language extends AppModel {
    public $table = 'bz1_languages';
    public $context = 'languages';

}