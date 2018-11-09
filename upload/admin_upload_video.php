<?php
ini_set('display_errors', 0);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(__FILE__)));

$file = $_FILES['myfile'];

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = time().'.'.$extension;
$fileType = $file['type'];
$fileSource = $file['tmp_name'];
$fileSize = $file['size'];

$tmpPath = JPATH_BASE. DS . 'media' . DS . 'tmp'. DS. $fileName;

if(copy($fileSource,$tmpPath)){
	echo json_encode(array(
		'status'=>true,
		'message'=>'Success',
		'filename'=> $fileName,
		'filepath' => JPATH_BASE.'/media/tmp/'.$fileName
	));
	exit();
}else{
	echo json_encode(array(
		'status'=>false,
		'message'=>'Error',
		'filename'=> $fileName
	));
	exit();
}