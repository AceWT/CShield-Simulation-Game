<?php

//add this to begining of every php script:
if (!defined('PATHROOT')) die('Invalid entry point.');

if (!is_file(PATHROOT."config.php"))
	die('CShield is not installed, copy config.php.example to config.php');
session_start();
require PATHROOT."config.php";
require PATHROOT."engine/db/medoo112.php"; //8.8.2016 - nova verzija medoo
require PATHROOT."engine/funcs/functions.php";
require PATHROOT."engine/funcs/gameobject_class.php";
require PATHROOT."engine/funcs/sessioninstance_class.php";
require PATHROOT."engine/funcs/html_class.php";

#set timezone
date_default_timezone_set('Europe/Zagreb');

#define database ($db) class
$db = new medoo(array(
	// required
	'database_type' => 'mysql',
	'database_name' => $config['db_db'],
	'server' => $config['db_host'],
	'username' => $config['db_user'],
	'password' => $config['db_pass'],

	// optional
    //'prefix' => 'PREFIX_', (not used, this is here as example)
	'port' => 3306,
	'charset' => 'utf8',
	// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
	'option' => array(
	PDO::ATTR_CASE => PDO::CASE_NATURAL
	)
));


$HTML = new HTMLClass();
$Instance = new SessionInstanceClass();


/*
Options +FollowSymLinks
RewriteEngine On
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^.*$ ./index.php
*/
$GET = explode("/", $_SERVER['REQUEST_URI']);
$GET['destination'] = (isset($_GET['destination']) && !empty($_GET['destination'])) ? trim(preg_replace("/[^a-zA-Z0-9 \/_\-:\.\,!\?\(\)]+/u", "",$_GET['destination'])) : false;
$GET_App = (isset($GET[1]) && !empty($GET[1])) ? trim(az09($GET[1])) : 'console';
$GET_Page = (isset($GET[2])) ? trim(preg_replace("/[^a-zA-Z0-9 \/_\-:\.\,!\?\(\)]+/u", "", $GET[2])) : 'index';
if (is_numeric($GET_Page))
{
    $pagenum = $GET_Page; //for paginator on index page, example www.axewebs.com/2
    $GET_Page = 'index';
}
else
{
    if (isset($_GET['page']))
        $pagenum = to09($_GET['page']);
    else
        $pagenum = 1;
}


$GET_Page = (empty($GET_Page)) ? 'index' : $GET_Page;
