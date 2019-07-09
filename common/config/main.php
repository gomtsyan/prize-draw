<?php

define('LOCAL', strpos($_SERVER['HTTP_HOST'],'prize.draw') != -1 ? 1 : 0);
define('MY_IP', $_SERVER['REMOTE_ADDR'] == '127.0.0.1'? : $_COOKIE['DEV_MODE'] == 1? $_SERVER['REMOTE_ADDR']:'');

$allowedIPs = [MY_IP];

if(LOCAL == 1){
    $db = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=draw_app',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ];
}else{
    $db = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=draw_app',//prod host & dbname
        'username' => '',//prod usrname
        'password' => '',//prod password
        'charset' => 'utf8'
    ];
}

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => $allowedIPs,
        ],
    ],
    'bootstrap' => [
        'gii'
    ]
];
