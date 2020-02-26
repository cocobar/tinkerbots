<?php
//微智分销解密修复、qq188872170、淘宝店铺：https://shop151264326.taobao.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

return array(
	'version' => '1.0',
	'id'      => 'goodscircle',
	'name'    => '好物圈',
	'v3'      => true,
	'menu'    => array(
		'title'     => '页面',
		'plugincom' => 1,
		'icon'      => 'page',
		'items'     => array(
			array('title' => '设置', 'route' => 'index')
		)
	)
);

?>
