<?php
define("APPID", "wxb92ee1e070e7ef13");
define("SECRET", "02adb0622ffdc234b0ac797ba1bbabc8");
define("ACCOUNT_ID", 1);
define("ROOT_PATH", "/services/tinkerbots/");
define("DOMAIN", "http://shop.tinkerbots.vip");
global $REDIS_CONFIG;
$REDIS_CONFIG = array(
    'weixin_cache' => array(
        array(
            'host'  => '127.0.0.1',
            'port'  => '6379',
            'password' => '',
            'dbN' => 1,
        ),
    ),

	'sys_cache' => array(
		array(
			'host'  => '127.0.0.1',
			'port'  => '6379',
			'password' => '',
			'dbN' => 0,
		),
	),

);

global $DB_CONFIG;
$DB_CONFIG = [
	'sys' => [
		[
			'host' => '127.0.0.1',
			'dbname' => 'tinkerbots',
			'user' => 'tinkerbots',
			'password' => 'aRxFSifbemJmXE8k',
			'port' => '3306',
			'charset' => 'utf8'
		]
	],
];

