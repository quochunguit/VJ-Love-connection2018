<?php
namespace App;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class Upload {

    var $newName = "";
    //default content type
    var $contentType = array('image/jpeg', 'image/pjpeg', 'image/jpeg', 'image/png', 'image/gif');
    var $exts = array('jpg', 'jpeg', 'gif', 'png');
    var $maxFileSize = 1; //Mb
    var $errorMessages = array(
        0 => "There is no error, the file uploaded with success",
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3 => "The uploaded file was only partially uploaded",
        4 => "No file was uploaded",
        6 => "Missing a temporary folder",
        7 => "Failed to write file to disk",
        8 => "A PHP extension stopped the file upload"
    );

    public function __construct() {
        
    }

    function setNewName($newname) {
        $this->newName = $newname;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function getExts() {
        return $this->exts;
    }

    public function setExts($exts) {
        $this->exts = $exts;
    }

    function deleteFile($file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    function getFileExtension($str) {
        $ext = end(explode(".", $str));
        return strtolower($ext);
    }

    function validFile($file) {
        if (!in_array($file['type'], $this->contentType) || !in_array($this->getFileExtension($file['name']), $this->exts)) {
            $message = printf('Please choose file: %s', implode(',', $this->getExts()));
            throw new UploadException($message);
        }
        return true;
    }

    function validSize($file) {
        $fileuploadsize = $file['size'];
        $fileuploadsize = $fileuploadsize / (1024 * 1024);
        $fileuploadsize = round($fileuploadsize, 2);
        if ($fileuploadsize > $this->maxFileSize) {
            $message = printf('Please choose file < %dMb', $this->maxFileSize);
            throw new UploadException($message);
        }
        return true;
    }

    function isValid($file) {

        $validFile = $this->validFile($file);
        $validSize = $this->validSize($file);
        return $validFile && $validSize;
    }

    public function uploadFile($folder, $file) {
        $return = array();
        try {
            $upload = $this->moveFile($folder, $file);
            if ($upload['status']) {
                $return['status'] = true;
                $return['filename'] = $upload['filename'];
                $return['file'] = $upload['file'];
            }
        } catch (UploadException $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    private function getErrorMessage($code) {
        if (array_key_exists($code, $this->errorMessages)) {
            return $this->errorMessages[$code];
        } else {
            return 'Unknow Error.';
        }
    }

    private function moveFile($folder, $file) {

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }
        try {
            $valid = $this->isValid($file);
        } catch (UploadException $e) {
            throw $e;
        }

        // replace spaces with underscores
        $fileName = str_replace(' ', '_', $file['name']);
        $fileName = strtolower($fileName);

        if ($this->newName) {
            $fileName = $this->newName . "." . $this->getFileExtension($file['name']);
        }

        $fileError = $file['error'];
        if ($fileError != UPLOAD_ERR_OK) {
            throw new UploadException($this->getErrorMessage($fileError));
        }
        if (!file_exists($folder . DS . $fileName)) {
            // create full filename
            $destFile = $folder . DS . $fileName;
        } else {
            if (!ini_get('date.timezone')) {
                ini_set('date.timezone', 'Europe/London');
            }
            $now = time();
            $fileName = $now . $fileName;
            $destFile = $folder . DS . $fileName;
        }
        $success = move_uploaded_file($file['tmp_name'], $destFile);
        if ($success) {
            return array(
                'status' => true,
                'file' => $destFile,
                'filename' => $fileName
            );
        }
        return false;
    }

}

class UploadException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}

?>
