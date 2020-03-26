<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$cardData = [
	//'card_id' => 'p40gXxHxgky_KKT3ijaAasOyN6Yc',
	//'card_id' => 'p40gXxHAmUpl85hYcqhd3TFLajyI',
  	//'card_id' => 'p40gXxHAmUpl85hYcqhd3TFLajyI',
  	'card_id' => 'p40gXxAuorS0AFfV0eDIVgAPeAF4',
	'member_card' => [
		'base_info' => [
			'center_url' => '',
			'custom_url_name' => '在线商城',
			'custom_url' => DOMAIN . '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile',
			'custom_url_sub_title' => '更多优惠',
			'promotion_url_name' => '积分商城',
			'promotion_url' => DOMAIN . '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=creditshop',
			'promotion_url_sub_title' => '礼品兑换',
			'pay_info' => [
				'swipe_card' => [
					'is_swipe_card' => true,
				],
			],
		],
/*
		'discount' => '2',
		'supply_bonus' => true,
		'bonus_url' => '',
		'bonus_rules' => '每消费1元增加1个积分，积分可到积分商城兑换商品',
		'bonus_rule' => [
			'cost_money_unit' => 100,
			'increase_bonus' => 1,
			'cost_bonus_unit' => 100,
			'reduce_money' => 100,
			'max_reduce_bonus' => 10000,
		],
		'supply_balance' => false,
		//'balance_url' => 'http://www.qq.com',
		//'balance_rules' => '充值满额可送，充1000送20，充2000送150',
 */


		'discount' => '',
		'supply_bonus' => true,
		'bonus_url' => '', 
		'auto_activate' => false,
		'activate_url' => DOMAIN .'/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=member.activation',
		"custom_field1" => [
			'name_type' => 'FIELD_NAME_TYPE_LEVEL',
			'url' => DOMAIN . '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=member',
		],
		"custom_field2" => [
			'name_type' => 'FIELD_NAME_TYPE_COUPON',
			'url' => DOMAIN . '/app/index.php?i=1&c=entry&m=ewei_shopv2&do=mobile&r=sale.coupon',
		],
	],
];
$url = "https://api.weixin.qq.com/card/update"; 
$reqData = json_encode($cardData, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
