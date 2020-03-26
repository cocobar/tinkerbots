<?php
require_once dirname(dirname(__FILE__)) . "/include/config.php";
class TokenManage {
	private static $__instance;
	private $__cache;

	private function __construct() {
		$this->__cache = new MyRedis();
		$this->__cache->use_redis("weixin_cache");
	}

	public static function getInstance() {
		if (!(self::$__instance instanceof self)) {
			self::$__instance = new self;
		}
		return self::$__instance;
	}

	public function getAccessToken() {
		$cacheData = $this->__cache->redis->get("access_token");
		if (!empty($cacheData)) {
			return $cacheData;
		}
		return $this->setAccessToken();
	}

	public function setAccessToken() {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APPID . '&secret=' . SECRET ;	
		$resData  = curl_request($url, "", "get");
		$resData = json_decode($resData, true);
		$this->__cache->redis->set('access_token', $resData['access_token'], 7200);
		return $resData['access_token'];
	}
}
