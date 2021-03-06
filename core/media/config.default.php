﻿<?php
/*
*
* Version Engine: 1.25.3
* Version File: 8
*
* 8.1
* add support XXX category
*
*/
if(!defined("IS_CORE")) {
echo "403 ERROR";
die();
}

define("BLOCK", 4*1024);
define("API_URL", "http://online-killer.pp.ua/api.php");

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') {
	$protocol = "https";
} else if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https') {
	$protocol = "https";
} else {
	$protocol = "http";
}

$config = array(
	"api_key" => "1234567890",
	"logs" => ERROR_FILE,
	"hosting" => true,//true - hosting, false - vps/vds
	"ip_test_shab" => array(
		"127.0.0.1",
	),
	"date_timezone" => 'Europe/Kiev',
	"cache" => array(
		"type" => CACHE_NONE,
		"server" => "localhost",
		"port" => 11211,
		"login" => "",
		"pass" => "",
		"path" => "/",
	),
	"skins" => array(
		"skins" => "main",
		"test_shab" => "",
		"mobile" => "",
	),
	"link" => array(
		"reg" => "/?reg",
		"lost" => "/?lost",
		"login" => "/?login",
		"logout" => "/?login&out",
		"add" => "/?add",
		"recover" => "/?recover",
	),
//минификаци¤
	"tpl_minifier" => true,
	"gzip" => "yes",
	"gzip_output" => true,
	"js_min" => true,

	"default_http_hostname" => "online-killer.pp.ua",
	"default_http_host" => $protocol."://online-killer.pp.ua/",
	'lang' => "ru",
	"charset" => "utf-8",

	"guest_level" => "0",
);

?>