<?php

/**
 * upload image script 
 */
ini_set('display_errors', 0);
include_once dirname(__FILE__) . '/libs/admin.upload.crop.php';
//echo dirname(__FILE__) . '/libs/media.class.php'; exit;

define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(__FILE__)));

$return = array();

$folder = DS . 'media' . DS . 'tmp';
$upload = new Upload();
$upload->isRename(1);
$upload->setNewName(time());
$upload_return = $upload->uploadFile($folder, $_FILES['myfile']);
//print_r($upload_return); exit;
if ($upload_return['status']) {
    $filename = $folder . DS . $upload_return['filename'];
    $return['status'] = true;
    $return['message'] = "success";
    $return['filepath'] = $filename;
    $return['filename'] = $upload_return['filename'];
} else {
    $return['status'] = false;
    $return['size'] = $_FILES['myfile']['size'];
    $return['message'] = $upload_return['message'];
}
echo json_encode($return);
exit();
