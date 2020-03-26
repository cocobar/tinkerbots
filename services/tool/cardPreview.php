<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
//$carId = "p40gXxHxgky_KKT3ijaAasOyN6Yc";
//$carId = "p40gXxAs4a0w4NTJutO3B_r2C9xY";
//$carId = "p40gXxKswifmAHGuSqGcEaXosLHI";
$carId = "p40gXxEdbFg6C46W_FPvm_5NrM_Q";
$code = "079545091102";
$data = [
	'touser' => 'o40gXxBschczLwy__yQNz7_sdTnc',
//	'touser' => 'o40gXxD_9zcdYEPYtTI3ki0DziAo',
	'msgtype' => 'wxcard',
	'wxcard' => [
		'card_id' => $carId,
	],

];
$url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview";
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
$result = json_decode($resData, true);
print_r($result);
