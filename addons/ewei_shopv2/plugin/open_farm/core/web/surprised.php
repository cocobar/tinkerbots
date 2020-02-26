<?php
//微智分销解密修复、qq188872170、淘宝店铺：https://shop151264326.taobao.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Surprised_EweiShopV2Page extends PluginWebPage
{
	/**
	private $table = 'ewei_open_farm_surprised';
	/**
	private $field = array('id', 'uniacid', 'value', 'category', 'probability', 'create_time');
	/**
	private $message = array('value' => '请设置彩蛋内容', 'category' => '请选择彩蛋种类', 'probability' => '请填写此彩蛋概率');

	/**
	public function main()
	{
		global $_W;
		require_once $this->template();
	}

	/**
	public function getList()
	{
		global $_W;
		global $_GPC;
		$condition = array('uniacid' => $_W['uniacid']);
		$currentPage = intval($_GPC['page']);
		$pageSize = 10;

		try {
			$sql = 'SELECT * FROM ' . tablename($this->table) . 'WHERE `uniacid`=' . $_W['uniacid'];
			$sql .= ' ORDER BY id DESC ';
			$sql .= ' LIMIT ' . ($currentPage - 1) * $pageSize . ',' . $pageSize;
			$surprisedList = pdo_fetchall($sql, $condition);
			$surprisedList = $this->getCoupon($surprisedList);
			$this->model->returnJson($surprisedList);
		}
		catch (Exception $e) {
			$this->model->errorMessage($_W['isajax'], $e->getMessage());
		}
	}

	/**
	public function getCoupon($surprisedList)
	{
		if (!$surprisedList || count($surprisedList) <= 0) {
			return $surprisedList;
		}

		$couponId = array();

		foreach ($surprisedList as $key => $value) {
			if ($value['category'] == '优惠券') {
				if ($value['value']) {
					$couponId[] = $value['value'];
				}
			}
		}

		if (count($couponId) <= 0) {
			return $surprisedList;
		}

		$couponId = implode(',', $couponId);
		$couponList = pdo_fetchall('SELECT  `id`,`couponname` FROM ' . tablename('ewei_shop_coupon') . (' WHERE `id` in(' . $couponId . ') '));

		foreach ($surprisedList as $surprisedKey => $surprisedValue) {
			foreach ($couponList as $couponKey => $couponValue) {
				if ($surprisedValue['value'] == $couponValue['id']) {
					$surprisedList[$surprisedKey]['couponname'] = $couponValue['couponname'];
				}
			}
		}

		return $surprisedList;
	}

	/**
	public function addInfo()
	{
		global $_W;
		global $_GPC;
		$data = $_GPC['__input'];
		$where = array('id' => $data['id']);
		$data['uniacid'] = $_W['uniacid'];
		$data['create_time'] = date('Y-m-d H:i:s');
		$this->checkInfo($data);
		$data = $this->model->removeUselessField($data, $this->field);

		if ($data['id']) {
			$surprisedInfo = pdo_update($this->table, $data, $where);
		}
		else {
			$surprisedInfo = pdo_insert($this->table, $data);
		}

		$this->model->returnJson($surprisedInfo);
	}

	/**
	public function getInfo()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['__input']['id'];
		$surprisedInfo = pdo_get($this->table, array('id' => $id));
		$this->model->returnJson($surprisedInfo);
	}

	/**
	public function deleteInfo()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['__input']['id'];
		$query = pdo_delete($this->table, array('id' => $id));
		$this->model->returnJson($query);
	}

	/**
	public function deleteAll()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['ids'];
		pdo_delete($this->table, array('id' => $id));
		show_json(1, '删除成功');
	}

	/**
	public function getCategory()
	{
		$table = 'ims_ewei_open_farm_surprised';
		$field = 'category';
		$categoryArr = $this->model->getEnumList($table, $field);
		$this->model->returnJson($categoryArr);
	}

	/**
	private function checkInfo($data)
	{
		$this->model->checkDataRequired($data, $this->message);
	}
}

?>