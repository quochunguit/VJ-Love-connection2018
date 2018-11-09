<?php

include_once dirname(dirname(dirname(__FILE__))) . '/vendor/phpthumb/ThumbLib.inc.php';

class Upload {

    var $contentType = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
    var $exts = array('JPG', 'JPEG', 'PNG', 'jpg', 'jpeg', 'png');
    var $maxFileSize = 5; //Mb
    var $minWidth = 280; //Check min width, no check 0
    var $minHeight = 280; //check min height, no check 0
    var $newName = "";
    var $isRename = false;
    var $isResize = false;
    var $newSize = '520x520';

    function setNewName($newName) {
        $this->newName = $newName;
    }

    function isRename($bool) {
        $this->isRename = $bool;
    }

    function isReSize($bool) {
        $this->isResize = $bool;
    }

    function newSize($strNewSize) {
        $this->newSize = $strNewSize;
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

    public function setNewSize($newSize) {
        $this->newSize = $newSize;
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

        if (!in_array($file['type'], $this->contentType) || !in_array(strtoupper($this->getFileExtension($file['name'])), $this->exts)) {
            $result['status'] = false;
            $result['message'] = 'Chỉ chấp nhận tập tin hình ảnh (jpg, jpeg, png). Vui lòng chọn lại!';
            return $result;
        }

        //check file size
        $fileuploadsize = $file['size'];
        $fileuploadsize = $fileuploadsize / (1024 * 1024);
        $fileuploadsize = round($fileuploadsize, 2);
        if ($fileuploadsize > $this->maxFileSize) {
            $result['status'] = false;
            $result['message'] = 'Tập tin phải có dung lượng nhỏ hơn hoặc bằng ' . $this->maxFileSize . ' MB';
            return $result;
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
            $filename = str_replace(' ', '_', $file['name']);
            $filename = strtolower($filename);

            if ($this->isRename && $this->newName) {
                $filename = $this->newName . ".png"; // . $this->getFileExtension($file['name']);
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

                        //***Check width height of current image upload
                        if ($this->minWidth > 0 || $this->minHeight > 0) {
                            if ($filename != null) {
                                $tmpFile = JPATH_BASE . DS . 'media' . DS . 'tmp' . DS . $filename;
                                list($width, $height, $type, $attr) = getimagesize($tmpFile);
                                $result['status'] = false;
                                $result['filename'] = $filename;
                                $result['width'] = $width;
                                $result['heigth'] = $height;
                                $result['type'] = $type;
                                $result['attr'] = $attr;

                                if ($this->minWidth > 0) {
                                    if ($width < $this->minWidth) {
                                        $result['message'] = 'Vui lòng chọn ảnh có chiều rộng lớn hơn hoặc bằng ' . $this->minWidth . 'px';
                                        return $result;
                                    }
                                }

                                if ($this->minHeight > 0) {
                                    if ($height < $this->minHeight) {
                                        $result['message'] = 'Vui lòng chọn ảnh có chiều cao lớn hơn hoặc bằng ' . $this->minHeight . 'px';
                                        return $result;
                                    }
                                }
                            }
                        }
                        //***End check width height of current image upload
                        //
                        //---=== Resize image upload CONTEST
                        if ($this->isResize) {
                            $tmpFile = JPATH_BASE . DS . 'media' . DS . 'tmp' . DS . $filename;
                            $tmpImageResize = JPATH_BASE . DS . 'media' . DS . 'tmp' . DS . 'image_resize' . DS . $filename;
                            $this->thumb($tmpFile, $tmpImageResize, $this->newSize); //new image(new size)
                            list($widthResize, $heightResize, $typeResize, $attrResize) = getimagesize($tmpImageResize);
                            //--Crop center
                            //$tmpImageResizeThumb = JPATH_BASE . DS . 'media' . DS . 'tmp' . DS . 'image_resize' . DS . 'thumb' . DS . $filename;
                            //$this->cropImageCenter($tmpFile, $tmpImageResizeThumb, '280x280');
                        }
                        //---=== End Resize image upload CONTEST
                        //
                        // if upload was successful
                        if ($success) {
                            //generate thumb image using first frame
                            $result['filename'] = $filename;
                            $result['size'] = $file['size'];
                            $result['type'] = $file['type'];
                            $result['width_resize'] = $widthResize;
                            $result['height_resize'] = $heightResize;
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

    public function thumb($org, $des, $size_desire) {
        if ($size_desire) {
            $size = explode('x', trim($size_desire));
            try {
                $thumb = PhpThumbFactory::create($org);
               $exif = @exif_read_data($org); //Need open mod in apache(php.ini):(extension=php_mbstring.dll và extension=php_exif.dll) 
               if (!empty($exif['Orientation'])) {
                   $orientation = $exif['Orientation'];
                   switch ($orientation) {
                       case 3:
                           $thumb->rotateImageNDegrees(180);
                           break;
                       case 6:
                           $thumb->rotateImage('CCW');
                           break;
                       case 8:
                           $thumb->rotateImage('CW');
                           break;
                       default:
                           break;
                   }
               }

                $thumb->resize($size[0], $size[1]);
                $thumb->setOptions(array('jpegQuality'=>80));
                $thumb->save($des,'jpg');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function cropImageCenter($folderContainImageRoot, $folderContainImageSave, $size_desire) {
        try {
            $size = explode('x', trim($size_desire));
            if(is_array($size)){
                $thumb_crop = PhpThumbFactory::create($folderContainImageRoot);
                $thumb_crop->cropFromCenter($size[0], $size[1]);
                $thumb_crop->save($folderContainImageSave);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
