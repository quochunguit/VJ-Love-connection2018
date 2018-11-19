<?php

namespace Core\Model;

class ResultSetMongoCursor implements \Iterator, \Zend\ServiceManager\ServiceLocatorAwareInterface {

    /**
      @var MongoCursor
     */
    protected $mongoCursor;
    protected $serviceLocator;
    protected $populate;

    public function __construct() {
        
    }

    public function getMongoCursor() {
        return $this->mongoCursor;
    }

    public function getPopulate() {
        if ($this->populate && is_array($this->populate)) {
            return $this->populate;
        }
    }

    public function setMongoCursor(\MongoCursor $mongoCursor) {
        $this->mongoCursor = $mongoCursor;
        return $this;
    }

    public function setPopulate($populate) {
        $this->populate = $populate;
        return $this;
    }

    public function current() {
        $data = $this->mongoCursor->current();
      
        $populate = $this->getPopulate();
        if ($populate) {
            foreach ($populate as $field => $info) {
                
                
                if (array_key_exists($field, $data)) {
                    $fieldObjectId = $data[$field];
                    $modelName = $info['model'];
                    $fields = $info['fields'];
                    $alias = $info['alias'];
                    if (!$modelName) {
                        continue;
                    }
                    $model = $this->getServiceLocator()->get($modelName);
                    if (!$model) {
                        continue;
                    }
                    $populateData = $model->getItemById($fieldObjectId, $fields);
                    $data[$alias] = $populateData;
                }else{
                    $fields = explode('.', $field);
                    if($fields && count($fields) > 1){
                        $attr = $fields[0];
                         if (array_key_exists($attr, $data)) {
                             $arrayData = $data[$attr];
                             if(is_object($arrayData) || is_array($arrayData)){
                               
                                foreach($arrayData as $key=>&$val){
                                    
                                    $fieldObjectId = $val[$fields[1]];
                                    $modelName = $info['model'];
                                    //$fields = $info['fields'];
                                    $alias = $info['alias'];
                                    if (!$modelName) {
                                        continue;
                                    }
                                    $model = $this->getServiceLocator()->get($modelName);
                                    if (!$model) {
                                        continue;
                                    }
                                    $populateData = $model->getItemById($fieldObjectId);
                                    //$val[$alias] = $populateData;
                                    $data[$attr][$key][$alias] = $populateData;
                                    //print_r($data); exit();
                                    
                                }
                             }
                         }
                    }
                }
            }
        }

        
        return $data;
    }

    public function valid() {
        return $this->mongoCursor->valid();
    }

    public function key() {
        return $this->mongoCursor->key();
    }

    public function count() {
        return $this->mongoCursor->count();
    }

    public function next() {
        $this->mongoCursor->next();
    }

    public function rewind() {
        $this->mongoCursor->rewind();
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
