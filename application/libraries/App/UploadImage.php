<?php

namespace App;

include_once VENDOR_DIR . '/phpthumb/ThumbLib.inc.php';

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}
class UploadImage {

    var $new_name = "";
    var $rename = 0;
    var $contentType = array('image/jpeg', 'image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'application/octet-stream');
    var $exts = array('jpg', 'jpeg', 'gif', 'png');
    var $maxFileSize = 2; //Mb
    var $newSize = '';
    var $resize = false;

    function setNewName($newname) {
        $this->new_name = $newname;
    }

    function isRename($bool) {
        $this->rename = $bool;
    }

    function isResize($flag) {
        $this->resize = $flag;
    }

    public function getNewSize() {
        return $this->newSize;
    }

    public function setNewSize($newSize) {
        $this->newSize = $newSize;
    }

    function deleteFile($folder, $filename) {
        // @unlink(JPATH_BASE . $folder . DS . $filename);
    }

    function getFileExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return strtolower($ext);
    }

    function uploadFile($folder, $file, $itemId = null) {
        // setup dir names absolute and relative

        $folder_url = $folder;

        $result = array();
        //check file type
        if (!in_array($file['type'], $this->contentType)) {
            $result['status'] = false;
            $result['message'] = 'Vui lòng chọn file hình ảnh';
            return $result;
            exit();
        }
        if (!in_array($this->getFileExtension($file['name']), $this->exts)) {
            $result['status'] = false;
            $result['message'] = 'Vui lòng chọn file hình ảnh';
            return $result;
            exit();
        }
        //check file size
        $fileuploadsize = $file['size'];
        $fileuploadsize = $fileuploadsize / (1024 * 1024);
        $fileuploadsize = round($fileuploadsize, 2);
        if ($fileuploadsize > $this->maxFileSize) {
            $result['status'] = false;
            $result['message'] = 'Bạn vui lòng chọn file hình ảnh có dung lượng nhỏ hơn 2M';
            return $result;
            exit();
        }
        // create the folder if it does not exist
        if (!is_dir($folder_url)) {
            mkdir($folder_url);
        }

        // if itemId is set create an item folder
        if ($itemId) {
            // set new absolute folder
            $folder_url = $folder . DIRECTORY_SEPARATOR . $itemId;
            // create directory
            if (!is_dir($folder_url)) {
                mkdir($folder_url);
            }
        }
        // replace spaces with underscores
        if (!empty($file['name'])) {
            $filename = str_replace(' ', '_', $file['name']);
            $filename = strtolower($filename);

            if ($this->rename && $this->new_name) {
                $filename = $this->new_name . "." . $this->getFileExtension($file['name']);
            }

            // assume filetype is false
            $typeOK = true;
            // if file type ok upload the file
            if ($typeOK) {
                // switch based on error code
                switch ($file['error']) {
                    case 0:
                        // check filename already exists
                        $now = '';
                        if (!file_exists($folder_url . DS . $filename)) {
                            // create full filename
                            $full_url = $folder_url . DS . $filename;

                            // upload the file
                            $success = move_uploaded_file($file['tmp_name'], $full_url);
                        } else {
                            // create unique filename and upload file
                            ini_set('date.timezone', 'Europe/London');
                            $now = time();
                            $filename = $now . $filename;
                            $full_url = $folder_url . DS . $filename;
                            $success = move_uploaded_file($file['tmp_name'], $full_url);
                        }
                       
                        // if upload was successful
                        if ($success) {
                            // save the url of the file

                            $result['filename'] = $filename;
                            $result['size'] = $file['size'];
                            $result['type'] = $file['type'];
                            $result['status'] = true;
                        } else {
                            $result['errors'][] = "Error uploaded $filename. Please try again.";
                        }
                        break;
                    case 3:
                        // an error occured
                        $result['errors'][] = "Error uploading $filename. Please try again.";
                        break;
                    default:
                        // an error occured
                        $result['errors'][] = "System error uploading $filename. Contact webmaster.";
                        break;
                }
            } elseif ($file['error'] == 4) {
                // no file was selected for upload
                $result['nofiles'][] = "No file Selected";
            } else {
                // unacceptable file type
                $result['errors'][] = "$filename cannot be uploaded. Acceptable file types: gif, jpg, png.";
            }
        }

        return $result;
    }

    function thumb($full_url) {
        if ($this->resize && $this->newSize) {
            $size = explode('x', $this->newSize);

            $thumb = PhpThumbFactory::create($full_url);
            $thumb->resize($size[0], $size[1]);
            $thumb->save($full_url);
        }
    }

}

?>
