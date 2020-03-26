<?php
require_once dirname(dirname(__FILE__)) . '/utils/TokenManage.php';
$token = TokenManage::getInstance()->setAccessToken();
echo $token . "\n";
