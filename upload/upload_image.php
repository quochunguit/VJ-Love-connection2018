<?php

ini_set('display_errors', 0);

$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
$baseUrl =
    ($https ? 'https://' : 'http://') .
    (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
        ($https && $_SERVER['SERVER_PORT'] === 443 ||
        $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
    substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

$baseUrl = str_replace('/upload','',$baseUrl);
//print_r($baseUrl); die;
define('BASE_URL', $baseUrl);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(__FILE__)));

include_once dirname(__FILE__) . '/libs/upload_image.php';

/*Config*/
$paramsPostName = 'image';
$isRename = true;
$isReSize = false;
$newName = time();
$newSize = '1000x1000';
/*End Config*/

$folder = DS . 'media' . DS . 'tmp';
$upload = new Upload();
$upload->isRename($isRename);
$upload->setNewName($newName);
$upload->isReSize($isReSize);
$upload->newSize($newSize);

$return = array();
if (!@$_FILES[$paramsPostName]) {
    $return['status'] = false;
    $return['message'] = "Lỗi trong quá trình gửi. Tập tin quá lớn hoặc định dạng không được hỗ trợ.<br>Vui lòng chọn tập tin khác.";
}else{
    $upload_return = $upload->uploadFile($folder, $_FILES[$paramsPostName]);
    if ($upload_return['status']) {
        $return['status'] = true;
        $return['message'] = "success";
        $return['path_root'] = BASE_URL . $folder . DS . $upload_return['filename'];
        $return['path_resize'] = BASE_URL.'/media/tmp/image_resize/'.$upload_return['filename'];
        $return['filename'] = $upload_return['filename'];
        $return['width_resize'] = $upload_return['width_resize'];
        $return['height_resize'] = $upload_return['height_resize'];
    }else{
        $return['status'] = false;
        $return['size'] = $_FILES[$paramsPostName]['size'];
        $return['message'] = $upload_return['message'];
    }
}

echo json_encode($return);
exit();
