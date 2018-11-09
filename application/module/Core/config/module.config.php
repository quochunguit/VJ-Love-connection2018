<?php

return array(
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'bzcmszf2'.SESSION_PREFIX,
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
    ),
    'controllers' => array(
        'invokables' => array(
            'Core\Controller\Core' => 'Core\Controller\CoreController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formMedia' => 'Core\Form\View\Helper\FormMedia',
            'formInputCustom' => 'Core\Form\View\Helper\FormInputCustom',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
        ),
    ),
);
