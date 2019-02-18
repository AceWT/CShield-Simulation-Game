<?php
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Vary: X-UserGrup,Accept-Encoding");


ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

define("PATHROOT", "../");
define("PATHHDD", "../app/console/game/harddrive/");
$page = ''; //define

//init all
require (PATHROOT . "engine/init.php");




$runApp = $GET_App;

//check if app exists
if (!is_dir(PATHROOT .'app/'.$runApp))
{
    $runApp = 'console'; //todo run error in console.
    die('INVALID CORE PATH');
}

require (PATHROOT . 'app/'.$runApp.'/engine/init.php');
$HTML->SetTitle('Facility Command Console');

require (PATHROOT . 'app/_common/template/header.php');
//run app overwrites
require (PATHROOT . 'app/'.$runApp.'/template/header.php');

require (PATHROOT . 'app/_common/template/footer.php');
//run app overwrites
require (PATHROOT . 'app/'.$runApp.'/template/footer.php');


//run page - catch output and set to $page variable.
ob_start();
$_currentpagePath = PATHROOT .'app/'.$runApp.'/pages/'.$GET_Page.'.php';
if (!is_file($_currentpagePath))
{
    echo '404 - Not Found';
}
else
    require ($_currentpagePath);
$page = ob_get_clean();

$HTML->RenderHeader();
echo $page;
$HTML->RenderFooter();
