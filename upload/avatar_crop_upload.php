<?php

ini_set('display_errors', 0);

define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(__FILE__)));

include_once dirname(__FILE__) . '/libs/avatar.crop.upload.php';

$return = array();
$folder = DS . 'media' . DS . 'tmp';
$upload = new Upload();
$upload->isRename(true);
$upload->setNewName(time());
$upload->isReSize(true);
$upload->newSize('720x720');

if (!@$_FILES['myfile']) {
    $return['status'] = false;
    $return['message'] = "Lỗi trong quá trình gửi. Tập tin quá lớn hoặc định dạng không được hỗ trợ.<br>Vui lòng chọn tập tin khác.";
    echo json_encode($return);
    exit();
}

$upload_return = $upload->uploadFile($folder, $_FILES['myfile']);
if ($upload_return['status']) {
    $filename = $folder . DS . $upload_return['filename'];
    $return['status'] = true;
    $return['message'] = "success";
    $return['filepath'] = $filename;
    $return['filename'] = $upload_return['filename'];
    $return['width_resize'] = $upload_return['width_resize'];
    $return['height_resize'] = $upload_return['height_resize'];
} else {
    $return['status'] = false;
    $return['size'] = $_FILES['myfile']['size'];
    $return['message'] = $upload_return['message'];
}
echo json_encode($return);
exit();
