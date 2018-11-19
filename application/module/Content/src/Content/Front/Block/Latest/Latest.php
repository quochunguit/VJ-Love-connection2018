<?php

namespace Content\Front\Block\Latest;

use Block\Front\Block\Type\AbstractType;

class Latest extends AbstractType {

    public function getDefaultParams() {
        return array('template' => 'content/block/latest');
    }

    public function getData() {

        $postService = $this->getServiceLocator()->get('PostService');
        $posts = $postService->getLatestPosts('post', 10);
        return $posts;
    }

}
