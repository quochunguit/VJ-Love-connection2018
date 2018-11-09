<?php
namespace Core\Entity;

class CoreEntity {
    
   public function toArray(){
        return get_object_vars($this);
    }
}

?>
