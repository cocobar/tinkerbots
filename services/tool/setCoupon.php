<?php
require_once dirname(dirname(__FILE__)) . '/utils/WxHandle.php';
$WxHandle = new WxHandle();
$data = array (
  'card_id' => 'p40gXxHuDDt8uqh6jSbBA2Yp6BGM',
  'discount' =>
  array (
    'base_info' =>
    array (
      'color' => 'Color070',
            'use_limit' => 100,
      'get_limit' => 1,
      'can_share' => false,
      'can_give_friend' => false,
      'center_title' => '',
      'center_url' => '',
      'center_sub_title' => '',
	  'center_app_brand_user_name' => '',
	  'center_app_brand_pass' => '',
/*
      'custom_url_name' => '',
      'custom_url_sub_title' => '',
	  'custom_app_brand_user_name' => '',
	  'custom_app_brand_pass' => '',
      'promotion_url_name' => '',
      'promotion_url_sub_title' => '',
	  'promotion_app_brand_user_name' => '',
	  'promotion_app_brand_pass' => '',
*/
    ),
  ),
);
$data = array (
  'card_id' => 'p40gXxNpHVCbYvNUCHaO2F2HlDNI',
  'cash' =>
  array (
    'base_info' =>
    array (
      'service_phone' => '020-6952232',
      'color' => 'Color010',
      'notice' => '使用时向服务员出示此券',
      'description' => '使用须知',
      'use_limit' => 8,
      'get_limit' => 8,
      'can_share' => false,
      'can_give_friend' => false,
      'use_all_locations' => true,
      'custom_url_name' => '',
      'custom_url_sub_title' => '',
      'custom_app_brand_user_name' => '',
      'custom_app_brand_pass' => '',
      'promotion_url_name' => '',
      'promotion_url_sub_title' => '',
      'promotion_app_brand_user_name' => '',
      'promotion_app_brand_pass' => '',
    ),
  ),
);
$url = "https://api.weixin.qq.com/card/update"; 
$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
$resData = $WxHandle->setWxData($url, $reqData);
print_r(json_decode($resData, true));
