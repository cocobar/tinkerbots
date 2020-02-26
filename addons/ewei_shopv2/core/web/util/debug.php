<?php
//微智分销解密修复、qq188872170、淘宝店铺：https://shop151264326.taobao.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Debug_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		phpinfo();
		exit();
	}
}

?>
