<?php

class Upload {

    var $new_name = "";
    var $rename = 0;
    var $contentType = array('image/jpeg', 'image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'application/octet-stream');
    var $exts = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
    var $maxFileSize = 6; //Mb

    function setNewName($newname) {
        $this->new_name = $newname;
    }

    function isRename($bool) {
        $this->rename = $bool;
    }

    public function getExts() {
        return $this->exts;
    }

    public function setExts($exts) {
        $this->exts = $exts;
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

        $folder_url = JPATH_BASE . $folder;
        $result = array();

        if (!in_array($file['type'], $this->contentType)) {
            $result['status'] = false;
            $result['message'] = 'Please choose files JPG, JPEG, PNG';
            return $result;
            exit();
        }

        if (!in_array($this->getFileExtension($file['name']), $this->exts)) {
            $result['status'] = false;
            $result['message'] = 'Please choose files JPG, JPEG, PNG';
            return $result;
            exit();
        }

        //check file size
        $fileuploadsize = round($file['size'] / (1024 * 1024), 2);
        if ($fileuploadsize > $this->maxFileSize) {
            $result['status'] = false;
            $result['message'] = 'Please select a smaller file size ' . $this->maxFileSize . 'M';
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
            $folder_url = JPATH_BASE . $folder . DS . $itemId;
            // create directory
            if (!is_dir($folder_url)) {
                mkdir($folder_url);
            }
        }
        // replace spaces with underscores
        if (!empty($file['name'])) {
            $filename = strtolower(str_replace(' ', '_', $file['name']));

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
                            $now = time();
                            $filename = $now . $filename;
                            $full_url = $folder_url . DS . $filename;
                            $success = move_uploaded_file($file['tmp_name'], $full_url);
                        }
                        // if upload was successful
                        if ($success) {
                            //generate thumb image using first frame
//                            $filename = rand(1, 9) . $filename;
//                            $des = $folder_url . DS . $filename;
//                            require_once dirname(dirname(dirname(__FILE__))) . '/phpthumb/ThumbLib.inc.php';
//                            $phpThumb = \PhpThumbFactory::create($full_url);
//                            $phpThumb->resize(950, 950);
//                            $phpThumb->save($des);

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

}

?>