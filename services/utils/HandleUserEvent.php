<?php
require_once dirname(dirname(__FILE__)) . "/include/config.php";
require_once ROOT_PATH . "framework/bootstrap.inc.php";
require_once ROOT_PATH . "addons/ewei_shopv2/defines.php";
require_once ROOT_PATH . "addons/ewei_shopv2/core/inc/functions.php";
class HandleUserEvent {
	public $message;
	private static $__instance;
	private $__cache;

	private function __construct($message) {
		$this->message = $message;
		$this->__cache = new MyRedis();
		$this->__cache->use_redis("sys_cache");
	}

	public static function getInstance($message) {
		if (!(self::$__instance instanceof self)) {
			self::$__instance = new self($message);
		}
		return self::$__instance;
	}

	public function dispatch() {
		switch($this->message['event']) {
			case "user_get_card":
			case "user_consume_card":
				return $this->delUserWxcardCache();
			case "update_member_card": // 积分变更，同步到数据库，到达升级条件，下发升级信息
				return $this->updateUserLevel();
			case "user_pay_from_pay_cell":// 用户买单，变更积分，下发信息
				return $this->updateUserBonus();
		}
	}

	// 用户领取卡券，核销卡券清缓存
	private function delUserWxcardCache() {
		$cacheKey = "ewei_shopv2_syscache_900bf3_1_" . APPID . "_user_wxcard:{$this->message['from']}:";
		$this->__cache->redis->del($cacheKey);
	}

	// 线下买单事件积分同步
	public function updateUserBonus() {
		global $_W;
		/*
		m('member')->setCredit($this->message['from'], 'credit1', 1, '线下消费获得');
		$bonus = intval($this->message['fee'] / 10);	
		if (empty($fee)) {
			return false;
		}	
*/
		
	}

	// 线下积分变更等级升级
	public function updateUserLevel() {
	}

}
