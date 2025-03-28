<?php
ini_set('display_errors',"");
define('DSN','mysql:host=localhost;charset=utf8;dbname=ec');
define('DB_USERNAME','ec_user');
define('DB_PASSWORD','axaG2A_F/jVo3H-S');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/EC/public_html');
require_once(__DIR__ .'/../lib/Controller/functions.php');
require_once(__DIR__ . '/autoload.php');
session_start();
$current_uri = $_SERVER["REQUEST_URI"];
$file_name = basename($current_uri);
define('TAX_RATE', 0.1);
define('VIEW_COUNT','10');
$goodsMod = new Ec\Model\Goods();
$goodsCon = new Ec\Controller\Goods();
$orderMod = new Ec\Model\Order();
$orderCon = new Ec\Controller\Order();
