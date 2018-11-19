<?php
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
//<iframe width="403" height="307" src="http://www.youtube.com/embed/videoid?rel=0" frameborder="0" allowfullscreen></iframe>
class Linkyoutube extends AbstractHelper {

    function getImage($str, $imgName = 'mqdefault.jpg'){
        if($str){
            $videoId = $this->getVideoId($str);
            if($videoId){
                return 'https://i.ytimg.com/vi/'.$videoId.'/'.$imgName;
            }
        }
        return '';
    }

    public function getVideoId($url){
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }
    }

    function isValidURL($value) {
        $value = trim($value);
        $validhost = true;

        if (strpos($value, 'http://') === false && strpos($value, 'https://') === false) {
            $value = 'http://' . $value;
        }

        //first check with php's FILTER_VALIDATE_URL
        if (filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === false) {
            $validhost = false;
        } else {
            //not all invalid URLs are caught by FILTER_VALIDATE_URL
            //use our own mechanism

            $host = parse_url($value, PHP_URL_HOST);
            $dotcount = substr_count($host, '.');

            //the host should contain at least one dot
            if ($dotcount > 0) {
                //if the host contains one dot
                if ($dotcount == 1) {
                    //and it start with www.
                    if (strpos($host, 'www.') === 0) {
                        //there is no top level domain, so it is invalid
                        $validhost = false;
                    }
                } else {
                    //the host contains multiple dots
                    if (strpos($host, '..') !== false) {
                        //dots can't be next to each other, so it is invalid
                        $validhost = false;
                    }
                }
            } else {
                //no dots, so it is invalid
                $validhost = false;
            }
        }

        //return false if host is invalid
        //otherwise return true
        return $validhost;
    }

    public function isYoutubeVideo($value) {
        $isValid = false;
        if ($this->isValidURL($value)) {

            $idLength = 11;
            $idOffset = 3;
            $idStarts = strpos($value, "?v=");
            if ($idStarts === FALSE) {
                $idStarts = strpos($value, "&v=");
            }
            if ($idStarts === FALSE) {
                $idStarts = strpos($value, "/v/");
            }
            if ($idStarts === FALSE) {
                $idStarts = strpos($value, "#!v=");
                $idOffset = 4;
            }
            if ($idStarts === FALSE) {
                $idStarts = strpos($value, "youtu.be/");
                $idOffset = 9;
            }
            if ($idStarts !== FALSE) {

                //there is a videoID present, now validate it

                $videoID = substr($value, $idStarts + $idOffset, $idLength);
                // $http = new HTTP("http://gdata.youtube.com");
                // $result = $http->doRequest("/feeds/api/videos/" . $videoID, "GET");
                $url = "http://gdata.youtube.com/feeds/api/videos/" . $videoID;
                $return = @simplexml_load_file($url);



                //returns Array('headers' => Array(), 'body' => String);
                //  $code = $result['headers']['http_code'];
                //did the request return a http code of 2xx?
                if ($return !== false) {
                    $isValid = true;
                }
            }
        }
        return $isValid;
    }

}

?>
