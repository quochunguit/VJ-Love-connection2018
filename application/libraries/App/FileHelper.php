<?php

namespace App;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class FileHelper {

    public static function deleteDir($dir) {
        $iterator = new \RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
            } else {
                @unlink($filename);
            }
        }
        return true;
    }

    public static function deleteFile($file) {
        if (file_exists($file)) {
            return @unlink($file);
        }
        return false;
    }

    public static function delete($file) {
        if (is_file($file)) {
            return self::deleteFile($file);
        } elseif (is_dir($file)) {
            return self::deleteDir($file);
        }
        return false;
    }

    public static function createFile($file) {
        return fopen($file, 'a');
    }

    public static function write($file, $data) {
        $splFile = new \SplFileObject($file, 'w');
        $splFile->fwrite($data);
    }

    public static function createDir($dir, $mode = 0775, $recursive = true) {
        return mkdir($dir, $mode, $recursive);
    }

    public static function getDirectoryIterator($path) {
        if (is_dir($path) || is_file($path)) {
            return new \DirectoryIterator($path);
        }else{
            throw new \Exception('Invalid Path');
        }
    }

    public static function getFileContent($file) {
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    public static function copy($source, $dest) {
        if (is_dir($source)) {
            $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                } else {
                    copy($file, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                }
            }
        } else {
            copy($source, $dest);
        }
    }

    public static function rename($src, $dest) {
        
    }

    public static function cpDir($src, $dest) {
        
    }

    public static function cpFile($src, $dest) {
        
    }

    public static function mvDir($src, $dest) {
        
    }

    public static function mvFile($src, $dest) {

        if (!is_dir($dest)) {
            mkdir($dest, 0775, true);
        }
        if (is_dir($dest)) {
            $dest = $dest . DS . self::getFileName($src);
        }

        if (!file_exists($src)) {
            throw new \Exception('File is not exists.');
        }
        rename($src, $dest);
    }

    public static function getFileName($file) {
        $info = new \SplFileInfo($file);
        return $info->getFilename();
    }

    public static function getFiles($path, $filters = array(), $recursive = true) {
        if (is_dir($path) || is_file($path)) {
            return new DirectoryIterator($path);
        }
    }

    public static function getDir($dir, $filters = array(), $recursive = true) {
        
    }

    public static function getTree($dir, $filters = array()) {
        
    }

    public static function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function getTypeOfFile($file) {
        if (!function_exists('mime_content_type')) {

            function mime_content_type($filename) {

                $mime_types = array(
                    'txt' => 'text/plain',
                    'htm' => 'text/html',
                    'html' => 'text/html',
                    'php' => 'text/html',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    'swf' => 'application/x-shockwave-flash',
                    'flv' => 'video/x-flv',
                    // images
                    'png' => 'image/png',
                    'jpe' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                    'ico' => 'image/vnd.microsoft.icon',
                    'tiff' => 'image/tiff',
                    'tif' => 'image/tiff',
                    'svg' => 'image/svg+xml',
                    'svgz' => 'image/svg+xml',
                    // archives
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'exe' => 'application/x-msdownload',
                    'msi' => 'application/x-msdownload',
                    'cab' => 'application/vnd.ms-cab-compressed',
                    // audio/video
                    'mp3' => 'audio/mpeg',
                    'qt' => 'video/quicktime',
                    'mov' => 'video/quicktime',
                    // adobe
                    'pdf' => 'application/pdf',
                    'psd' => 'image/vnd.adobe.photoshop',
                    'ai' => 'application/postscript',
                    'eps' => 'application/postscript',
                    'ps' => 'application/postscript',
                    // ms office
                    'doc' => 'application/msword',
                    'rtf' => 'application/rtf',
                    'xls' => 'application/vnd.ms-excel',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    // open office
                    'odt' => 'application/vnd.oasis.opendocument.text',
                    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                );

                $ext = strtolower(array_pop(explode('.', $filename)));
                if (array_key_exists($ext, $mime_types)) {
                    return $mime_types[$ext];
                } elseif (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME);
                    $mimetype = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                    return $mimetype;
                } else {
                    return 'application/octet-stream';
                }
            }

        }
        $type = mime_content_type($file);
        return $type;
    }

    public static function upload($file, $dest, $options = array()) {

        $errorMessages = array(
            0 => "There is no error, the file uploaded with success",
            1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
            2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
            3 => "The uploaded file was only partially uploaded",
            4 => "No file was uploaded",
            6 => "Missing a temporary folder",
            7 => "Failed to write file to disk",
            8 => "A PHP extension stopped the file upload"
        );

        $fileUploadCode = $file['error'];
        $filename = $file['tmp_name'];

        $path = rtrim($dest, '\,/');

        if ($fileUploadCode != UPLOAD_ERR_OK) {
            //error
            $message = $errorMessages[$fileUploadCode];
            $status = false;
        } else {
            $des = $path . DS . $file['name'];
            if (file_exists($des)) {
                $finfo = pathinfo($des);
                $ext = $finfo['extension'];
                $name = $finfo['filename'];
                $i = 1;
                $newdes = $path . DS . $name . '_' . $i . '.' . $ext;
                while (file_exists($newdes)) {
                    $i++;
                    $newdes = $path . DS . $name . '_' . $i . '.' . $ext;
                }
                $des = $newdes;
            }
            if (move_uploaded_file($filename, $des)) {
                $message = 'Upload successful.';
                $status = true;
            }
        }
        return array('status' => $status, 'message' => $message);
    }

    public static function download($filename) {
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));

        switch ($file_extension) {
            case "pdf": $ctype = "application/pdf";
                break;
            case "exe": $ctype = "application/octet-stream";
                break;
            case "msi": $ctype = "application/octet-stream";
                break;
            case "zip": $ctype = "application/zip";
                break;
            case "doc": $ctype = "application/msword";
                break;
            case "xls": $ctype = "application/vnd.ms-excel";
                break;
            case "ppt": $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif": $ctype = "image/gif";
                break;
            case "png": $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg": $ctype = "image/jpg";
                break;
            default: $ctype = "application/force-download";
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filename));
        self::readfileChunked("$filename");
    }

    private static function readfileChunked($filename, $retbytes = true) {
        $chunksize = 1 * (1024 * 1024); // how many bytes per chunk 
        $buffer = '';
        $cnt = 0;

        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt;
        }
        return $status;
    }

}
