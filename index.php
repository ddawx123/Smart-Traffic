<?php
define('APP_PATH', dirname(__FILE__).'/');
require_once(APP_PATH.'library/common.php');
$common = new Common();
$common->Main(@$_GET['c']);