<?php

$HTML->SetTitle('CSHIELD::Mainframe');

//check if is activated
$mainframeStatus = $Instance->Game->GetSetting('mainframestatus');

if ($mainframeStatus == 0)
{
	header('Location: /console');
	exit;
}

//mainframe is online

//check login
$isLoggedIn = $Instance->Game->GetSetting('mainframeloggedin');
if ($isLoggedIn == 0)
{
	header('Location: /mainframe/login');
	exit;
}

//echo $mainframeStatus;





