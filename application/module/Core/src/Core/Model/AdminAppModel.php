<?php

namespace Core\Model;
use Zend\Db\Adapter\Adapter;

class AdminAppModel extends AppModel{
    
    public function __construct(Adapter $adapter) {
        parent::__construct($this->context);
        $this->adapter = $adapter;
        $this->initialize();
    }
    
}
