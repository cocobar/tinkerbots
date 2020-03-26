<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$data = [
	//'card_id' => 'p40gXxHxgky_KKT3ijaAasOyN6Yc',
	//'card_id' => 'p40gXxM1nrCtSevrtEIn9w45DWH0',
	//'card_id' => 'p40gXxHAmUpl85hYcqhd3TFLajyI',
	'card_id' => 'p40gXxAuorS0AFfV0eDIVgAPeAF4',
	'is_open' => false,
];
$url = "https://api.weixin.qq.com/card/paycell/set"; 
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
