<?php
require_once dirname(dirname(__FILE__)) . "/include/config.php";
require_once dirname(dirname(__FILE__)) . "/utils/TokenManage.php";
class WxHandle {
	public $db;
	public $redis;
	public $token;
	public function __construct() {
		$this->db = new MyDB();
		$this->db->use_db('sys');
		$this->redis = new MyRedis();
		$this->redis->use_redis("weixin_cache");
		$this->token = TokenManage::getInstance()->getAccessToken();	
	}
	public function setWxData($url, $data, $method = 'post') {
		$resData  = curl_request($url . '?access_token=' .$this->token, $data, $method);
		return $resData;
	}

	public function getMemberInfo($cardId, $cardCode) {
		if (empty($cardId) || empty($cardCode)) {
			return false;
		}
		$data = [
			'card_id' => $cardId,
			'code' => $cardCode,
		];
		$url = "https://api.weixin.qq.com/card/membercard/userinfo/get";
		$reqData = json_encode($data, JSON_UNESCAPED_UNICODE);
		$resData = $this->setWxData($url, $reqData);
		$userInfo = json_decode($resData, true);
		if (empty($userInfo) || $userInfo['errcode'] != 0) {
			return false;
		}
		return $userInfo;
	}

}
