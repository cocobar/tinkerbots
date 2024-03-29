<?php
//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]);
}

class Upgrade_new_EweiShopV2Page extends SystemPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$auth = get_auth();
		$versionfile = IA_ROOT . '/addons/ewei_shopv2/version.php';
		$updatedate = date('Y-m-d H:i', filemtime($versionfile));
		$version = EWEI_SHOPV2_VERSION;
		$release = EWEI_SHOPV2_RELEASE;
		$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
		$ip = gethostbyname($_SERVER['HTTP_HOST']);
		$setting = setting_load('site');
		$id = isset($setting['site']['key']) ? $setting['site']['key'] : (isset($setting['key']) ? $setting['key'] : '0');
		load()->func('communication');
		$uni = pdo_fetchall('SELECT uniacid FROM ' . tablename('ewei_shop_sysset'), array(), 'uniacid');
		$options = array();
		if (!empty($uni) && date('w') % 7 == 1) {
			$uni_str = implode(',', array_keys($uni));
			$account_wechats = pdo_fetchall('SELECT uniacid,`name`,`key` FROM ' . tablename('account_wechats') . ' WHERE uniacid IN (' . $uni_str . ')');
			$members = pdo_fetchall('SELECT COUNT(*) as count,uniacid FROM ' . tablename('ewei_shop_member') . '  GROUP BY uniacid', array(), 'uniacid');
			$orders = pdo_fetchall('SELECT SUM(price) as price,uniacid,COUNT(*) as `count` FROM ' . tablename('ewei_shop_order') . ' WHERE `status`=3 AND paytime>' . (strtotime(date('y-m-d')) - 3600 * 24 * 7) . ' GROUP BY uniacid', array(), 'uniacid');

			foreach ($account_wechats as $key => $val) {
				$options[$key]['name'] = $val['name'];

				if (isset($members[$val['uniacid']])) {
					$options[$key]['member_count'] = (int) $members[$val['uniacid']]['count'];
				}

				if (isset($orders[$val['uniacid']])) {
					$options[$key]['order_price'] = (double) $orders[$val['uniacid']]['price'];
					$options[$key]['order_count'] = (int) $orders[$val['uniacid']]['count'];
				}
			}
		}

		$plugins = scandir(EWEI_SHOPV2_PLUGIN);
		array_shift($plugins);
		array_shift($plugins);
		$resp = ihttp_request(EWEI_SHOPV2_NEW_AUTH_URL, array('ip' => $ip, 'id' => $id, 'code' => $auth['code'], 'domain' => $domain, 'options' => $options, 'dirplugin' => $plugins));
		$result = @json_decode($resp['content'], true);
		if (!empty($result['result']['has_unauth']) && is_array($result['result']['has_unauth'])) {
			load()->func('file');

			foreach ($result['result']['has_unauth'] as $value) {
				rmdirs(EWEI_SHOPV2_PLUGIN . $value);
			}
		}

		$auth_str = '';

		if (!empty($result['result']['auth_date_end'])) {
			$residue = (strtotime($result['result']['auth_date_end']) - time()) / 3600 / 24;
			if ($residue < 30 && 0 < $residue) {
				$auth_str = '您的授权即将到期, 点击续费';
			}
			else {
				if (!empty($residue) && $residue < 0) {
					$auth_str = '您的授权已经过期, 点击续费';
				}
			}
		}

		$url = 'https://app.we7shop.com/apps/detail?id=6';
		include $this->template();
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$plugins = pdo_fetchall('select `identity` from ' . tablename('ewei_shop_plugin'), array(), 'identity');
		load()->func('db');
		load()->func('communication');
		$check = (int) $_GPC['check'];
		set_time_limit(0);
		$auth = get_auth();
		$version = defined('EWEI_SHOPV2_VERSION') ? EWEI_SHOPV2_VERSION : '2.0.0';
		$release = defined('EWEI_SHOPV2_RELEASE') ? EWEI_SHOPV2_RELEASE : '201605010000';
		$file_md5 = $this->fileGlob(EWEI_SHOPV2_PATH, true);
		$start = microtime(true);
		$resp = ihttp_post(EWEI_SHOPV2_NEW_AUTH_URL . '/index', array('ip' => $auth['ip'], 'id' => $auth['id'], 'code' => $auth['code'], 'domain' => trim(preg_replace('/http(s)?:\\/\\//', '', trim($_W['siteroot'], '/'))), 'version' => $version, 'release' => $release, 'manual' => $check, 'file_md5' => json_encode($file_md5), 'plugins' => array_keys($plugins), 'phpversion' => PHP_VERSION_ID));
		$end = microtime(true);

		if (empty($resp['content'])) {
			show_json(0);
		}

		$result = json_decode($resp['content'], true);

		if (is_array($result)) {
			$templatefiles = '';
			$upgrade = $result['result'];

			if ($result['status'] == 1) {
				file_put_contents(__DIR__ . '/file.txt', '----文件改动----<br/>');

				if (!empty($upgrade['file_path'])) {
					foreach ($upgrade['file_path'] as $index => $path) {
						file_put_contents(__DIR__ . '/file.txt', $index + 1 . ': ' . $path . '<br/>', FILE_APPEND);
					}
				}

				$database = array();

				if (!empty($upgrade['structs'])) {
					foreach ($upgrade['structs'] as $remote) {
						$name = substr($remote['tablename'], 4);
						$local = $this->table_schema(pdo(), $name);
						$sqls = db_table_fix_sql($local, $remote);

						if (!empty($sqls)) {
							$database = array_merge($database, $sqls);
						}
					}

					foreach ($database as $index => &$b) {
						if (isset($upgrade['file_path'])) {
							if ($index == 0) {
								file_put_contents(__DIR__ . '/file.txt', '<br/>----数据库改动----<br/>', FILE_APPEND);
							}

							file_put_contents(__DIR__ . '/file.txt', $index + 1 . ': ' . $b . '<br/>', FILE_APPEND);
						}

						$b = self::base64UrlEncode($b);
					}

					unset($b);
				}

				$log = base64_decode($upgrade['log']);
				show_json(1, array('result' => 1, 'version' => $upgrade['version'], 'release' => $upgrade['release'], 'files' => $upgrade['files'], 'database' => $database, 'upgrades' => $upgrade['upgrades'], 'log' => nl2br($log), 'new_log' => $upgrade['new_log'], 'templatefiles' => $templatefiles, 'hasfile' => (int) !empty($upgrade['file_path'])));
			}

			show_json(0, $upgrade['message']);
		}

		if (is_file(EWEI_SHOPV2_PATH . 'tmp')) {
			@unlink(EWEI_SHOPV2_PATH . 'tmp');
		}

		show_json(0, $resp['content']);
	}

	/**
     *
     */
	public function process()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			load()->func('communication');
			load()->func('file');
			$auth = get_auth();
			$type = trim($_GPC['type']);
			$content = is_array($_GPC['content']) ? $_GPC['content'] : trim(htmlspecialchars_decode($_GPC['content'], ENT_QUOTES));
			$version = trim($_GPC['version']);
			$release = trim($_GPC['release']);

			switch ($type) {
			case 'database':
				load()->func('db');

				if (empty($content)) {
					show_json(0, array('type' => $type, 'message' => $content));
				}

				$content = is_base64($content) ? self::base64UrlDecode($content) : $content;

				if (pdo_query($content) === false) {
					show_json(0, array('type' => $type, 'message' => $content));
				}

				show_json(1, array('type' => $type));
				break;

			case 'files':
				$resp = ihttp_post(EWEI_SHOPV2_NEW_AUTH_URL . '/download', array('ip' => $auth['ip'], 'id' => $auth['id'], 'code' => $auth['code'], 'domain' => trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/'))), 'md5' => $content));
				$ret = json_decode($resp['content'], true);

				if (is_array($ret['result'])) {
					foreach ((array) $ret['result']['files'] as $files) {
						if (strexists($files['path'], 'pcsite/')) {
							$dirpath = dirname($files['path']);
							mkdirs(IA_ROOT . '/' . $dirpath);
							$content = base64_decode($files['content']);
							file_put_contents(IA_ROOT . '/' . $files['path'], $content);
							mkdirs(EWEI_SHOPV2_PATH . $dirpath);
							copy(IA_ROOT . '/' . $files['path'], EWEI_SHOPV2_PATH . '/' . $files['path']);
						}
						else {
							$dirpath = dirname($files['path']);
							mkdirs(EWEI_SHOPV2_PATH . $dirpath);
							$content = base64_decode($files['content']);
							file_put_contents(EWEI_SHOPV2_PATH . $files['path'], $content);
						}
					}

					show_json(1, array('type' => 'files'));
				}

				show_json(0, array('type' => 'files'));
				break;

			case 'upgrades':
				if (empty($content) || $content == 'success') {
					$this->updateComplete($version, $release);
					show_json(2, array('type' => 'upgrades'));
				}

				if ($content == 'success_noload') {
					$this->updateComplete($version, $release);
					show_json(3, array('type' => 'upgrades'));
				}

				$updatepath = EWEI_SHOPV2_PATH . 'tmp';

				if (!is_dir($updatepath)) {
					mkdir($updatepath, 493, true);
				}

				$updatefile = $updatepath . ('/upgrade-' . $release . '.php');
				$content = base64_decode($content);

				if (!empty($content)) {
					file_put_contents($updatefile, $content);
					require $updatefile;
				}

				show_json(1, array('type' => 'upgrades'));
				break;
			}
		}

		show_json(2, array('type' => 'upgrades'));
	}

	/**
     * @param $db
     * @param string $tablename
     * @return array
     */
	protected function table_schema($db, $tablename = '')
	{
		$result = $db->fetch('SHOW TABLE STATUS LIKE \'' . trim($db->tablename($tablename), '`') . '\'');

		if (empty($result)) {
			return array();
		}

		$ret['tablename'] = $result['Name'];
		$ret['charset'] = $result['Collation'];
		$ret['increment'] = $result['Auto_increment'];
		$result = $db->fetchall('SHOW FULL COLUMNS FROM ' . $db->tablename($tablename));

		foreach ($result as $value) {
			$temp = array();
			$type = explode(' ', $value['Type'], 2);
			$temp['name'] = $value['Field'];
			$pieces = explode('(', $type[0], 2);
			$temp['type'] = $pieces[0];
			$temp['length'] = rtrim($pieces[1], ')');
			$temp['null'] = $value['Null'] != 'NO';
			$temp['signed'] = empty($type[1]);
			$temp['increment'] = $value['Extra'] == 'auto_increment';
			$temp['default'] = $value['Default'];
			$ret['fields'][$value['Field']] = $temp;
		}

		$result = $db->fetchall('SHOW INDEX FROM ' . $db->tablename($tablename));

		foreach ($result as $value) {
			$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
			$ret['indexes'][$value['Key_name']]['type'] = $value['Key_name'] == 'PRIMARY' ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
			$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
		}

		return $ret;
	}

	/**
     * @param $version
     * @param $release
     */
	protected function updateComplete($version, $release)
	{
		load()->func('file');
		file_put_contents(EWEI_SHOPV2_PATH . 'version.php', '<?php if(!defined(\'IN_IA\')) {exit(\'Access Denied\');}if(!defined(\'EWEI_SHOPV2_VERSION\')) {define(\'EWEI_SHOPV2_VERSION\', \'' . $version . '\');}if(!defined(\'EWEI_SHOPV2_RELEASE\')) {define(\'EWEI_SHOPV2_RELEASE\', \'' . $release . '\');}');
		cache_delete('cloud:modules:upgradev2');
		$time = time();
		global $my_scenfiles;
		my_scandir(IA_ROOT . '/addons/ewei_shopv2');

		foreach ($my_scenfiles as $file) {
			if (!strexists($file, '/ewei_shopv2/data/') && !strexists($file, 'version.php')) {
				@touch($file, $time);
			}
		}

		rmdirs(IA_ROOT . '/addons/ewei_shopv2/tmp');
	}

	public function checkversion()
	{
		load()->model('cache');
		file_put_contents(IA_ROOT . '/addons/ewei_shopv2/version.php', '<?php if(!defined(\'IN_IA\')) {exit(\'Access Denied\');}if(!defined(\'EWEI_SHOPV2_VERSION\')) {define(\'EWEI_SHOPV2_VERSION\', \'2.0.0\');}if(!defined(\'EWEI_SHOPV2_RELEASE\')) {define(\'EWEI_SHOPV2_RELEASE\', \'201605010000\');}');
		pdo_update('modules', array('version' => '3.0.0'), array('name' => 'ewei_shopv2'));
		cache_clean();
		header('location: ' . webUrl('system/auth/upgrade'));
		exit();
	}

	public function log()
	{
		global $_W;
		global $_GPC;
		$plugins = pdo_fetchall('select `identity` from ' . tablename('ewei_shop_plugin'), array(), 'identity');
		$auth = get_auth();
		$version = defined('EWEI_SHOPV2_VERSION') ? EWEI_SHOPV2_VERSION : '2.0.0';
		$release = defined('EWEI_SHOPV2_RELEASE') ? EWEI_SHOPV2_RELEASE : '201605010000';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		load()->func('communication');
		$resp = ihttp_post(EWEI_SHOPV2_AUTH_URL . 'log', array('ip' => $auth['ip'], 'id' => $auth['id'], 'code' => $auth['code'], 'domain' => trim(preg_replace('/http(s)?:\\/\\//', '', trim($_W['siteroot'], '/'))), 'version' => $version, 'release' => $release, 'manual' => 1, 'plugins' => array_keys($plugins), 'pindex' => $pindex, 'psize' => $psize));
		$res = @json_decode($resp['content'], true);
		$count = 0;
		$log = '';

		if (is_array($res)) {
			$count = $res['count'];
			$log = $res['log'];
			$new_log = $res['new_log'];
		}

		$pager = pagination2($count, $pindex, $psize);
		include $this->template('system/auth/log');
	}

	public function fileGlob($path, $md5 = false, $only_dir = false, $recursive = true)
	{
		$res = array();

		if (substr($path, -1) !== '*') {
			$path = $path . '*';
		}

		if (strexists($path, EWEI_SHOPV2_DATA)) {
			return array();
		}

		foreach (glob($path) as $file) {
			if ($file != '.' && $file != '..') {
				$relative_path = str_replace(EWEI_SHOPV2_PATH, '', $file);

				if (is_dir($file)) {
					if ($recursive) {
						$res = array_merge($res, $this->fileGlob($file . '/*', $md5, $only_dir, $recursive));
					}

					if ($only_dir) {
						$res[$relative_path] = $file;
						continue;
					}
				}
				else if ($md5) {
					if (1024 * 3 < filesize($file) / 1024) {
						continue;
					}

					$res[$relative_path] = md5_file($file);
				}
				else {
					$res[$relative_path] = $file;
				}
			}
		}

		return $res;
	}

	static public function base64UrlEncode($input)
	{
		return base64_encode($input);
	}

	static public function base64UrlDecode($input)
	{
		return base64_decode($input);
	}

	public function filechange()
	{
		if (is_file(__DIR__ . '/file.txt')) {
			echo file_get_contents(__DIR__ . '/file.txt');
		}
		else {
			echo '没有文件更新!';
		}
	}
}

?>
