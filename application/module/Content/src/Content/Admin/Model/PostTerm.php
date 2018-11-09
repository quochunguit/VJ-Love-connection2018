<?php

namespace Content\Admin\Model;


use Core\Model\AppModel;

class PostTerm extends AppModel {

    public $table = 'bz1_posts_terms';
    public $context = 'post_term';

    
    
    public function save($postId, $category, $ext='post'){
        if(!is_array($category)){
            $category = array($category);
        }
        //delete old data 
        $isDelete = $this->delete(array('post_id'=>$postId, 'extension'=>$ext));
        foreach($category as $termId){
            $this->insert(array('post_id'=>$postId,'term_id'=>$termId,'extension'=>$ext));
        }
        
        
    }
    public function getTermIdsByPostId($postId, $extension='post'){
        $return = array();
        $entries = $this->findAll(array('wheres'=>array('post_id'=>$postId, 'extension'=>$extension)));
        
        foreach ($entries as $entry) {
            $return[] = $entry['term_id'];
        }
        return $return;
    }

   

}