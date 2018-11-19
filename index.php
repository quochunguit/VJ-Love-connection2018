<?php

/** Set basic htpassword ***/
// list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
//  if (!isset($_SERVER['PHP_AUTH_USER'])) {
//   header('WWW-Authenticate: Basic realm="bzcms"');
//   header('HTTP/1.0 401 Unauthorized');
//   exit;
//  } else {
//   if ($_SERVER['PHP_AUTH_USER'] == 'htuser' && $_SERVER['PHP_AUTH_PW'] == 'htpass') {             
//   } else {
//    header('WWW-Authenticate: Basic realm="Protected Page"');
//    header('HTTP/1.0 401 Unauthorized');
//    exit;
//   }
// }


define('WEB_ROOT', __DIR__);
define('APPLICATION_LIB', WEB_ROOT . '/application/libraries');
define('VENDOR_DIR', WEB_ROOT . '/vendor');
define('VENDOR_INCLUDE_DIR', WEB_ROOT . '/vendor_include');
set_include_path(implode(PATH_SEPARATOR, array(APPLICATION_LIB, get_include_path())));
define('DS', DIRECTORY_SEPARATOR);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$isDebug = @$_GET['debug'] == 1 ? 1 : 0;
ini_set('display_errors', $isDebug);
error_reporting(E_ALL);

include 'init_autoloader.php'; // Setup autoloading
Zend\Mvc\Application::init(include __DIR__ . '/application/config/application.config.php')->run(); //Run the application