<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$carId = "p40gXxAuorS0AFfV0eDIVgAPeAF4";
$code = "672090967159";
$data = [
	'card_id' => $carId,
	'code' => $code,
];
$url = "https://api.weixin.qq.com/card/membercard/unactivate";
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
