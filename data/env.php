<?php
//default enviroment
$env = 'development';
if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV']) {
   $env = $_SERVER['APPLICATION_ENV'];
}
if($env=='production'){
   ini_set('display_errors',0);
   error_reporting(0);
}elseif($env=='development'){
   ini_set('display_errors',0);
   error_reporting(E_ALL & ~E_NOTICE);
}

return $env;