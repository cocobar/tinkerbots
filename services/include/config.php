<?php
$env = "production";
require_once "{$env}.config.php";
require_once dirname(dirname(__FILE__)) . "/utils/Common.func.php";
require_once dirname(dirname(__FILE__)) . "/utils/REDIS.php";
require_once dirname(dirname(__FILE__)) . "/utils/DB.php";
