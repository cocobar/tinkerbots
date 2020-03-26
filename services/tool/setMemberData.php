<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$carId = "p40gXxPADLu7Stuc0k9-B2JnjEJg";
$code = "759901567801";
$data = [
	'card_id' => $carId,
	'code' => $code,
];
$url = "https://api.weixin.qq.com/card/membercard/userinfo/get";
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
$userInfo = json_decode($resData, true);

$data = [
	'card_id' => $carId,
	'code' => $code,
	'bonus' => $userInfo['bonus'] + 100,
	'add_bonus' => 100,
	'record_bonus' => '测试消费100元，赠送100分',
	'custom_field_value1' => '普通会员',
];
$url = "https://api.weixin.qq.com/card/membercard/updateuser"; 
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
