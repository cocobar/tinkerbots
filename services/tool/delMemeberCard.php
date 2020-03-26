<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$data = [
	'card_id' => 'p40gXxHAmUpl85hYcqhd3TFLajyI',
];
$url = "https://api.weixin.qq.com/card/delete"; 
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
