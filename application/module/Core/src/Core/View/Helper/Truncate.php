<?php
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
class Truncate extends AbstractHelper {

    function truncate($phrase, $max_words = 40, $type = '') {
        $phrase = preg_replace('/(<)([img])(\w+)([^>]*>)/', '', $phrase);
        $phrase = $this->strip_html_tags($phrase);
        
        if($type == 'char'){
            $length = $this->unicode_strlen($phrase);
            if($length > $max_words){
                mb_internal_encoding("UTF-8");
                $sentence = mb_substr($phrase, 0, $max_words);

                $lastsentence = mb_substr($phrase, $max_words, $length);
                $firtSpace = mb_strpos($lastsentence, ' ');
                if($firtSpace){
                    $string_append = mb_substr($lastsentence, 0, $firtSpace);
                    $phrase = $sentence.$string_append.'...';
                }else{
                    $lastSpace = mb_strrpos($sentence, ' ');
                    $phrase = mb_substr($sentence, 0, $lastSpace).'...';
                }
            }
        }else{
            $phrase_array = explode(' ',$phrase);
            if(count($phrase_array) > $max_words && $max_words > 0)
                $phrase = implode(' ',array_slice($phrase_array, 0, $max_words))."...";
        }
        
        return $phrase;
    }
    
    function unicode_strlen ($s) {
        $c = preg_match_all ('/./u', $s, $m);
        return ($c);
    }

    function unicode_substr ($s, $start=0, $length=false) {
        if (!is_numeric($start)) 
            $start = 0;
        if (false === $length) 
            $length = unicode_strlen($s);
        $maxLen = $this->unicode_strlen($s) - $start;
        if ($length > $maxLen) 
            $length = $maxLen;
        if (!$start) {
            return preg_replace ('/^(.{'.$length.'}).*$/mu', '\1', $s);
        } else {
            return preg_replace ('/^.{'.$start.'}(.{'.$length.'}).*$/mu', '\1', $s);
        }
    }
    
    public function truncatebychar($phrase, $max_chars = 100) {
        $phrase = preg_replace('/(<)([img])(\w+)([^>]*>)/', '', $phrase);
        $phrase = $this->strip_html_tags($phrase);
        $i = 0; $sentence = '';
        while($i < $max_chars){
            $sentence .= $phrase[$i];
            $i++;
        }
        while($phrase[$i] != ' '){
            $sentence .= $phrase[$i];
            $i++;
        }
        return $sentence;
    }
    
    public function plain($string, $max=38) {
        //$string = preg_replace("/&#?[a-z0-9]+;/i","",$string);
        $return = $this->truncate( $string, $max);
        $order   = array("\r\n", "\n", "\r","&rquote;","&nbsp;","&ndash;","&rdquo;","&ldquo;","&rsquo;");

        $replace = ' ';
        $return = strip_tags($return);

        $return = str_replace($order, $replace, $return);
        
        $return = str_replace('&#39;', "'", $return);
        $return = str_replace('&quot;', "'", $return);

        return $return;
    }

    function strip_html_tags( $text ) {
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
                ),
                array(
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
                ),
                $text );
        return strip_tags( $text );
    }

    function TrimStr($str) {
        $str = trim($str);
        for($i=0;$i < strlen($str);$i++) {

            if(substr($str, $i, 1) != " ") {

                $ret_str .= trim(substr($str, $i, 1));

            }
            else {
                while(substr($str,$i,1) == " ") {
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
                ),
                array(
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
                ),
                $text );
        return ( $text );
    }
    
    function getDay($str){        
        $day = substr($str,strrpos($str,"-")+1,2);     
        return $day;
    }

    function getMonth($str){        
        $month = substr($str,strpos($str,"-")+1,2);            
        if($month < 10)$month=substr($month,1,1);
        return $month;
    }

    /* $strPosition: last(uu tien lay tu phia sau), first(uu tien lay tu phia truoc)
     * Vidu: Le Thi Hong Hanh ->last: ...Hanh -->first: Lê...
     */
    public function fixNameLong($name, $numWordGet = 4, $strPositionGet = 'last') {
        $nameFix = 'error';
        $maxWord = $numWordGet;
        $nameArr = explode(' ', trim($name));
        $numNameWord = count($nameArr);
        if ($numNameWord <= $maxWord) {
            $nameFix = $name;
        } else {
            if ($strPositionGet == 'last') { //Lay bat dau index = $numWordGet cho den cuoi chuoi
                $numStart = $numNameWord - $maxWord;
                $numEnd = $numNameWord;
            } else { //Lay tu dau chuoi cho den tu co index = $numWordGet
                $numStart = 0;
                $numEnd = $numWordGet - 1;
            }

            $fixNameArr = array();
            for ($i = $numStart; $i <= $numEnd; $i++) {
                $fixNameArr[] = $nameArr[$i];
            }
            $nameFix = $strPositionGet == 'last' ? '... ' . implode(' ', $fixNameArr) : implode(' ', $fixNameArr) . ' ...';
        }

        return $nameFix;
    }

    /* Giấu số điện thoại dạng: xxxxxx312 hoặc 123xxxxxx
     * $strPositionHide = 'first': xxxxxx312 , 'last': 123xxxxxx
     */
    public function showSecurePhone($phone, $numHide = 7, $charHide ='x', $strPositionHide = 'first') {  
        $phoneFix = $phone;
        if($phone){
            $phoneArr = str_split($phone);

            $maxChar = $numHide;
            $numChar = count($phoneArr);

            if ($strPositionHide == 'last') { 
                $numStart = $numChar - $maxChar;
                $numEnd = $numChar;
            } else { 
                $numStart = 0;
                $numEnd = $numHide - 1;
            }

            $fixPhoneArr = array();
            foreach ($phoneArr as $key => $value) {
               
                if($key >= $numStart && $key<=$numEnd){
                    $fixPhoneArr[] = $charHide;
                }else{
                    $fixPhoneArr[] = $value;
                }

            }

            $phoneFix =  implode('', $fixPhoneArr);
        }

        return $phoneFix;
    }

}

?>
