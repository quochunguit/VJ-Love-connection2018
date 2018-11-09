<?php

namespace App;

class YouTubeHelper {

    var $linkYoutube = '';
    var $videoId = '';

    public function getLinkYoutube() {
        return $this->linkYoutube;
    }

    public function setLinkYoutube($linkYoutube) {
        $this->linkYoutube = $linkYoutube;
    }

    public function getVideoId() {
        return $this->videoId;
    }

    /**
     * check url is valid url
     * @param type $value
     * @return boolean 
     */
    function isValidURL($url) {
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

    function getVideoYoutubeId() {
        if ($this->linkYoutube) {
            $output = array();
            $string = parse_url($this->linkYoutube);
            parse_str($string['query'], $output);
            if ($output['v']) {
                $this->videoId = $output['v'];
            }
            return $this->videoId;
        }
    }

    function getImageDefault() {
        if (empty($this->videoId)) {
            $this->videoId = $this->getVideoYoutubeId();
        }
        $link = "http://img.youtube.com/vi/" . $this->videoId . "/default.jpg";
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $link = "https://img.youtube.com/vi/" . $this->videoId . "/default.jpg";
        }

        return $link;
    }

    function getEmbedLink() {
        if (empty($this->videoId)) {
            $this->videoId = $this->getVideoYoutubeId();
        }
        $link = "http://www.youtube.com/embed/" . $this->videoId . "?&amp;wmode=transparent&amp;rel=0";
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $link = "https://www.youtube.com/embed/" . $this->videoId . "?&amp;wmode=transparent&amp;rel=0";
        }

        return $link;
    }

    function getIframeYouTube($options = array()) {
        $link = $this->getEmbedLink();

        $default = array(
            'width' => 480,
            'height' => 300,
            'frameborder' => 0,
            'allowfullscreen' => 'allowfullscreen',
            'src' => $link
        );

        $attrs = array_merge($default, $options);

        $attr = '';
        foreach ($attrs as $key => $val) {
            $attr.= "$key=" . '"' . $val . '"';
        }

        $frame = "<iframe $attr></iframe>";
        return $frame;
    }

}
