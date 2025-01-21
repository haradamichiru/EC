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
$goodsMod = new Ec\Model\Goods();
define('RATE', $goodsMod->settings()[0]->tax / 100);
define('POSTAGE', $goodsMod->settings()[0]->postage);
