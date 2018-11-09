<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Plaintext extends AbstractHelper {

    //put your code here
    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function direct() {
        
    }

    function truncate($phrase, $max_words = 40) {
        $phrase = preg_replace('/(<)([img])(\w+)([^>]*>)/', '', $phrase);
        $phrase = $this->strip_html_tags($phrase);
        $phrase_array = explode(' ', $phrase);
        if (count($phrase_array) > $max_words && $max_words > 0)
            $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)) . "...";

        return $phrase;
    }

    public function plaintext($string, $max = 38) {
        //$string = preg_replace("/&#?[a-z0-9]+;/i","",$string);
        $return = $this->truncate($string, $max);
        $order = array("\r\n", "\n", "\r", "&rquote;", "&nbsp;", "&ndash;", "&rdquo;", "&ldquo;", "&rsquo;");

        $replace = ' ';
        $return = strip_tags($return);

        $return = str_replace($order, $replace, $return);

        $return = str_replace('&#39;', "'", $return);
        $return = str_replace('&quot;', "'", $return);

        return $return;
    }

    function strip_html_tags($text) {
        $text = preg_replace(
                array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
                ), array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
                ), $text);
        return strip_tags($text);
    }

    function TrimStr($str) {
        $str = trim($str);
        for ($i = 0; $i < strlen($str); $i++) {

            if (substr($str, $i, 1) != " ") {

                $ret_str .= trim(substr($str, $i, 1));
            } else {
                while (substr($str, $i, 1) == " ") {
                    $i++;
                }
                $ret_str.= " ";
                $i--; // ***
            }
        }
        return $ret_str;
    }

    function remove_tags($text) {
        $text = preg_replace(
                array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
                ), array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
                ), $text);
        return ( $text );
    }

    function getDay($str) {
        $day = substr($str, strrpos($str, "-") + 1, 2);
        return $day;
    }

    function getMonth($str) {
        $month = substr($str, strpos($str, "-") + 1, 2);
        if ($month < 10)
            $month = substr($month, 1, 1);
        return $month;
    }

}

?>