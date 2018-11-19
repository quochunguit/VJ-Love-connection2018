<?php

namespace Core\Model;

class Database {

    static $instance;
    private $db;
    private $mongoClient;

    private function __construct() {
        if (!$this->db) {
            $this->openConnection();
        }
    }

    public function openConnection() {
        try {
          
            $this->mongoClient = new \MongoClient("mongodb://" . DB_HOST . ':' . DB_PORT);
            $this->db = $this->mongoClient->selectDB(DB_NAME);
        } catch (\MongoConnectionException $e) {
			//echo $e->getMessage();
            exit('Can not connect to DB');
        }
    }

    public function closeConnection() {
        $this->getMongoClient()->close();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDb() {

        return $this->db;
    }

    public function getMongoClient() {

        return $this->mongoClient;
    }

}

