<?php

ini_set('display_errors', 0);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(__FILE__)));

if ($_GET['file'] == 'yes') {
    $ext = strtolower(end(explode('.', $_FILES['Filedata']['name'])));
    if (!in_array($ext, array('3gp', 'wmv', 'flv', 'mpeg', 'avi', 'mp4', 'rm'))) {
        print_r(array('status' => false));
    }
}

$folder = JPATH_BASE . DS . 'media' . DS . 'tmp';
$filename = time() . rand(1, 999999) . '.' . strtolower(end(explode('.', $_FILES['Filedata']['name'])));
$origin = $folder . DS . $filename;

if ($_FILES['Filedata']) {
    move_uploaded_file($_FILES['Filedata']['tmp_name'], $origin);
    $return = $filename;
} else {
    $return = false;
}

print_r($return);
exit();
