<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$data = [
	'card_id' => 'p40gXxHAmUpl85hYcqhd3TFLajyI',
	'bind_old_card' => [
		'name' => '老会员绑定',
		'url' => DOMAIN .'/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=member.activation',
	],
	'required_form' => [
		'common_field_id_list' => [
			'USER_FORM_INFO_FLAG_MOBILE',
			'USER_FORM_INFO_FLAG_NAME',
		],
	],
];
$url = "https://api.weixin.qq.com/card/membercard/activateuserform/set"; 
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
