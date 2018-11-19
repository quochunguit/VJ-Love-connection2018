<?php
/*Docs: https://framework.zend.com/manual/2.4/en/modules/zend.mail.smtp.options.html*/
/*Disable security account google: https://www.google.com/settings/u/2/security/lesssecureapps*/
return array(
    'mail' => array(
        'transport' => array(
            'options' => array(
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'connection_class' => 'login', //“smtp”, “plain”, “login”, or “crammd5”, and defaults to “smtp”.
                'connection_config' => array(
                    'username' => 'tuananhbizzon@gmail.com',
                    'password' => '22121988abcXyz',
                    'ssl' => 'tls'
                ),
            ),
        ),
    )
);
