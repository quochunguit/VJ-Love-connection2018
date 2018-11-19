<?php
$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
$baseUrl =
    ($https ? 'https://' : 'http://') .
    (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
        ($https && $_SERVER['SERVER_PORT'] === 443 ||
        $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
    substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));

define('BASE_URL', $baseUrl);
define('DOMAIN', $_SERVER['HTTP_HOST']);
define('CKEDITOR_PATH', BASE_URL);

/*-- Media --*/
define('BASE_URL_MEDIA', BASE_URL . '/media');
define('BASE_URL_MEDIA_IMAGE', BASE_URL_MEDIA . '/images');
/*-- End Media --*/

/*-- Skin --*/
define('BASE_URL_SKIN', BASE_URL . '/skin');
define('BASE_URL_SKIN_FONT', BASE_URL . '/skin/front/mytheme/fonts');
define('BASE_URL_SKIN_IMAGE', BASE_URL . '/skin/front/mytheme/images');
define('BASE_URL_SKIN_JAVASCRIPT', BASE_URL . '/skin/front/mytheme/scripts');
define('BASE_URL_SKIN_JS', BASE_URL . '/skin/front/mytheme/scripts');
define('BASE_URL_SKIN_STYLESHEET', BASE_URL . '/skin/front/mytheme/styles');
define('BASE_URL_SKIN_DCUSTOM', BASE_URL . '/skin/front/mytheme/zdcustom');
/*-- End Skin --*/

/**********-- COMMON CONFIG --*******************/

define('SESSION_PREFIX', 'mytheme'); /* Prefix session this site (You should change it) */
define('LIVE_HOST_NAME', 'mytheme.websitetesting.club'); /* Change it if live (ex: http://subdomain.your_domain.com ---> hostname: subdomain.your_domain.com) */

/*-- Admin secure url --*/
define('ADMIN_SECURE', 'ad2min');
define('ADMIN_SECURE_COOKIE_CODE', SESSION_PREFIX.'RKMVY0/A68IPV$`G92B{q90%K:4ZNl');
define('ADMIN_LOGIN_FAIL', 5); /* LOGIN fail allow number if continues wrong then block */
define('ADMIN_BLOCK_MINUTES', 1); /* Block account login after ADMIN_BLOCK_MINUTES */
/*-- End Admin secure url --*/

/*-- APP FACEBOOK --*/
define('FB_APP_ID', '343007442942720');
define('FB_APP_SECRET', 'bcdaeb5ce1c363eee004bffac29d48f6');
define('FB_FANPAGE_NAME', '');
/*-- END APP FACEBOOK --*/

/*-- EMAIL SENDER --*/
define('EMAIL_SEND_FROM_EMAIL', 'mytheme@gmail.com');
define('EMAIL_SEND_FROM_NAME', 'info');
/*-- END EMAIL SENDER --*/

/*-- API YOUTUBE --*/
define('YOUTUBE_APP_NAME', 'YOUTUBEUPLOAD');
define('YOUTUBE_CLIENT_ID', '');
define('YOUTUBE_CLIENT_SECRET', '');
define('YOUTUBE_REDIRECT_URI','http://localhost/bz-cms-custom/admin/contest/');
/*-- END API YOUTUBE --*/

/*--GOOGLE Variable (View demo in contact controller) --*/
define('RECAPTCHA_SITE_KEY', '6LexSCQTAAAAAN5sWVoeQURZBi8ZMNtQz5nzqICm');
define('RECAPTCHA_SECRET_KEY', '6LexSCQTAAAAADas2bGedtm8FIzSAn96TwtTuRpX');
define('GOOGLE_APP_ID', '649699108866-4krpcd1p1klnjvn1ddr08ktujmr9dtsa.apps.googleusercontent.com');

/*--END GOOGLE Variable --*/

/*--SMS Variable (View demo in contact controller) --*/

define('USERNAME', 'emerald_verify');
define('PASSCODE', 'vmg7a');
define('SENDER', 'Verify');

/*--END SMS Variable --*/



/**********-- END COMMON CONFIG --*******************/