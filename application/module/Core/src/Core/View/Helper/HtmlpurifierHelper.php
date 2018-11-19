<?php
 class HtmlpurifierHelper{
     public static function clean($var){
         $purifier = Zend_Registry::get('purifier');
         $content = $purifier->purify($var);
        
         return html_entity_decode($content);
    }
     
 }

