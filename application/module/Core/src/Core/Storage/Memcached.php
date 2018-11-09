<?php
namespace Core\Storage;
class Memcached implements StorageInterface {

    protected $host = 'localhost';
    protected $port = '11211';
    protected $lifetime = 0;
    protected $memcache = null;

    public function __construct() {
        $this->memcache = new Memcache();
        $this->memcache->connect($this->host, $this->port) or die("Error : Memcache is not ready");
    }

    public function __destruct() {
        session_write_close();
        $this->memcache->close();
    }

    public function close() {
        return true;
    }

    public function destroy($id) {
        return $this->memcache->delete("sessions/{$id}");
    }

    public function gc($maxlifetime) {
         return true;
    }

    public function open($save_path, $name) {
        $this->lifetime = ini_get('session.gc_maxlifetime');
        return true;
    }

    public function read($id) {
        $tmp = $_SESSION;
        $_SESSION = json_decode($this->memcache->get("sessions/{$id}"), true);
        if (isset($_SESSION) && !empty($_SESSION) && $_SESSION != null) {
            $new_data = session_encode();
            $_SESSION = $tmp;
            return $new_data;
        } else {
            return "";
        }
    }

    public function write($id, $data) {
        $tmp = $_SESSION;
        session_decode($data);
        $new_data = $_SESSION;
        $_SESSION = $tmp;
        return $this->memcache->set("sessions/{$id}", json_encode($new_data), 0, $this->lifetime);
    }

}

