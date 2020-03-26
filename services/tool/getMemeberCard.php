<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$carId = "p40gXxHxgky_KKT3ijaAasOyN6Yc";
$data = [
	'car_id' => $carId,
	'begin_date' => '2019-12-15',
	'end_date' => '2019-12-20',
];
$url = "https://api.weixin.qq.com/datacube/getcardmembercarddetail";
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
$result = json_decode($resData, true);
print_r($result);
