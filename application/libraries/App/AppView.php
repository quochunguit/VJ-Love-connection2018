<?php
namespace App;
class AppView {

    public static function makeOption($selected, $options) {
        $opt_str = "";
        foreach ($options as $key => $value) {
            if ($selected == $key && strlen($selected) > 0) {
                $select = " selected ";
            } else {
                $select = "";
            }

            $opt_str .= "<option value='$key' $select >$value</option>";
        }

        return $opt_str;
    }

    public static function statusContestOptions($selected) {
        $options = array("" => '-- all --', '0' => 'Unpublished', '1' => 'Published', '2'=>'Rejected');
        return AppView::makeOption($selected, $options);
    }

    public static function statusOptions($selected) {
        $options = array("" => '-- all --', '0' => 'unpublished', '1' => 'published');
        return AppView::makeOption($selected, $options);
    }
    public static function albumTypeOptions($selected) {
        $options = array("" => '-- all --', 'Album' => 'Album', 'Video' => 'Video','Article'=>'Article');
        return AppView::makeOption($selected, $options);
    }
    public static function readOptions($selected) {
        $options = array("" => '-- all --', '0' => 'unreaded', '1' => 'readed');
        return AppView::makeOption($selected, $options);
    }

    public static function featuredOptions($selected) {
        $options = array("" => '-- all --', '0' => 'No', '1' => 'Yes');
        return AppView::makeOption($selected, $options);
    }

    public static function yesNoOptions($selected, $msgYes = 'Yes', $msgNo = 'No') {
        $options = array("" => '-- all --', '0' => $msgNo , '1' => $msgYes);
        return AppView::makeOption($selected, $options);
    }

    public static function objectOptions($selected) {
        $options = array('posts' => 'Post', 'album' => 'Album');
        return AppView::makeOption($selected, $options);
    }

 

}

