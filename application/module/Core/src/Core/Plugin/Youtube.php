<?php

namespace Core\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

require_once VENDOR_INCLUDE_DIR.'/google/autoload.php';
require_once VENDOR_INCLUDE_DIR.'/google/Client.php';
require_once VENDOR_INCLUDE_DIR.'/google/Service/YouTube.php';

class Youtube extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $serviceManager;

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

            $videoID = $this->getVideoId($value);

            $url = "http://gdata.youtube.com/feeds/api/videos/" . $videoID.'?v=2&fields=title';

            $return = @simplexml_load_file($url);

            if ($return !== false) {
                $isValid = true;
            }

        }
        return $isValid;
    }
    public function getVideoId($url){
        $idLength = 11;
        $idOffset = 3;
        $idStarts = strpos($url, "?v=");
        if ($idStarts === FALSE) {
            $idStarts = strpos($url, "&v=");
        }
        if ($idStarts === FALSE) {
            $idStarts = strpos($url, "/v/");
        }
        if ($idStarts === FALSE) {
            $idStarts = strpos($url, "#!v=");
            $idOffset = 4;
        }
        if ($idStarts === FALSE) {
            $idStarts = strpos($url, "youtu.be/");
            $idOffset = 9;
        }
        if ($idStarts !== FALSE) {
            $videoID = substr($url, $idStarts + $idOffset, $idLength);
        }

        return $videoID;
    }
    public function getVideoTitle($url){
        $id = $this->getVideoId($url);
        $videoTitle = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$id}?v=2&fields=title");
        preg_match("/<title>(.+?)<\/title>/is", $videoTitle, $titleOfVideo);
        return $titleOfVideo[1];
    }

    public function getUrlImgPreview($url, $name='hqdefault'){
        $id = $this->getVideoId($url);
        $imgUrl = 'http://img.youtube.com/vi/'.$id.'/'.$name.'.jpg';
        return $imgUrl;
    }

    //--UPload video to youtube

    public function writeAccessTKFile($content){
        $myfile = @fopen(WEB_ROOT. DS."media".DS."zg_client_access_token.txt", "w") or die("Unable to open file!");
        @fwrite($myfile, $content);
        fclose($myfile);
    }

    public function readAccessTKFile(){
        $my_file = WEB_ROOT. DS."media".DS."zg_client_access_token.txt";
        $handle = @fopen($my_file, 'r');
        $data = @fread($handle,filesize($my_file));
        //$data = file_get_contents($my_file);

        return $data;
    }

    public function deleteAccessTKFile(){
        $my_file = WEB_ROOT. DS."media".DS."zg_client_access_token.txt";
        unlink($my_file);
    }

    public function clientAccessToken($data){
          
        $application_name = $data['youtube_app_name']; 
        $client_id = $data['youtube_client_id'];
        $client_secret = $data['youtube_client_secret'];
        $youtube_redirect_url = $data['youtube_redirect_url'];
        $scope = array('https://www.googleapis.com/auth/youtube.upload');
                       
        // Client init
        $client = new \Google_Client();

        $client->setApplicationName($application_name);
        $client->setClientId($client_id);
        $client->setAccessType('offline');
        $client->setScopes($scope);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($youtube_redirect_url);

         if($data['code']){
            if($this->readAccessTKFile() == null){
                $client->authenticate($data['code']);
            }
            
            $this->writeAccessTKFile($client->getAccessToken());
          }
    }


    /************************************************
    * $application_name, $client_id, $client_secret : Application name created: http://console.developers.google.com
    * $youtube_redirect_url: This is url will to redirect then allow permission Youtube
    * $scope: Permission need user allowd..
    */

    public function processUpload($data){
      
        $application_name = $data['youtube_app_name']; 
        $client_id = $data['youtube_client_id'];
        $client_secret = $data['youtube_client_secret'];
        $youtube_redirect_url = $data['youtube_redirect_url'];
        $scope = array('https://www.googleapis.com/auth/youtube.upload');

        $videoPath = $data['video_path'];   //Path WEB_ROOT.DS......             
        $videoTitle = $data['video_title']; //String
        $videoDescription = $data['video_description']; //String
        $videoCategory = $data['video_category']; //Number category youtube
        $videoTags = $data ['video_tags']; //This is array: array("youtube", "tutorial");
            
        try{
            // Client init
            $client = new \Google_Client();

            $client->setApplicationName($application_name);
            $client->setClientId($client_id);
            $client->setAccessType('offline');

            if($this->readAccessTKFile() != null){
                $key = $this->readAccessTKFile();
                $client->setAccessToken($key);
            }
            
            $client->setScopes($scope);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($youtube_redirect_url);
         
            if ($client->getAccessToken()) {
         
                /**
                 * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
                 */
                if($client->isAccessTokenExpired()) {
                    $newToken = json_decode($client->getAccessToken());
                    $client->refreshToken($newToken->refresh_token);
                    $this->writeAccessTKFile($client->getAccessToken());
                }
         
                $youtube = new \Google_Service_YouTube($client);
                
                // Create a snipet with title, description, tags and category id
                $snippet = new \Google_Service_YouTube_VideoSnippet();
                $snippet->setTitle($videoTitle);
                $snippet->setDescription($videoDescription);
                $snippet->setCategoryId($videoCategory);
                $snippet->setTags($videoTags);
         
                // Create a video status with privacy status. Options are "public", "private" and "unlisted".
                $status = new \Google_Service_YouTube_VideoStatus();
                $status->setPrivacyStatus('public');
         
                // Create a YouTube video with snippet and status
                $video = new \Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);
         
                // Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
                // for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
                $chunkSizeBytes = 1 * 1024 * 1024;
         
                // Setting the defer flag to true tells the client to return a request which can be called
                // with ->execute(); instead of making the API call immediately.
                $client->setDefer(true);
         
                // Create a request for the API's videos.insert method to create and upload the video.
                $insertRequest = $youtube->videos->insert("status,snippet", $video);
         
                // Create a MediaFileUpload object for resumable uploads.
                $media = new \Google_Http_MediaFileUpload(
                    $client,
                    $insertRequest,
                    'video/*',
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($videoPath));
         
                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($videoPath, "rb");
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }
         
                fclose($handle);
         
                /**
                 * Video has successfully been upload, now lets perform some cleanup functions for this video
                 */
                if ($status->status['uploadStatus'] == 'uploaded') {
                    //Actions to perform for a successful upload
                    $returnArr = array('status' => true, 'info'=>$status, 'message' => 'Upload to Youtube success!!');
                }
         
                // If you want to make other calls after the file upload, set setDefer back to false
                $client->setDefer(true);

                return $returnArr;
         
            } else{
                // @TODO Log error
                //echo 'Problems creating the client';

                $authUrl = $client->createAuthUrl();

                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                    $protocol = "https";
                } else
                    $protocol = "http";

                $authUrl = str_replace('&redirect_uri=https', '&redirect_uri=' . $protocol, $authUrl);
                $returnArr = array('status' => false, 'url_auth' => $authUrl, 'message' => 'Please accept permission of Youtube!');

                return $returnArr;
            }
         
        } catch(\Google_Service_Exception $e) {
            $msg = "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage() . 
            "\n Stack trace is ".$e->getTraceAsString();

            $this->deleteAccessTKFile(); //Delete File
            $returnArr = array('status' => false,  'message' => $msg);
            return $returnArr;

        }catch (\Exception $e) {
             $msg = "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage() . 
            "\n Stack trace is ".$e->getTraceAsString();

            $this->deleteAccessTKFile(); //Delete File
            $returnArr = array('status' => false,  'message' => $msg);
            return $returnArr;
        }
    }
    //--End upload video to youtube

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}
