<?php


if (!isset($cmdparams['u']) && !isset($cmdparams['p']))
{
   $response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Required parameters are missing. u or/and p','format' => 'line'),
                    )
            );
   return; 
}

//load user from file:
$console_instancesetuser_userfile = PATHROOT. 'app/console/game/harddrive/bin/users/'.az09($cmdparams['u']).'.txt';
if (!is_file($console_instancesetuser_userfile))
{
	$response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Specified user does not exist.','format' => 'line'),
                    )
            );
    return; 
}
$console_instancesetuser_data = file_get_contents($console_instancesetuser_userfile);
$console_instancesetuser_data_ex = explode(':',$console_instancesetuser_data);

if ($console_instancesetuser_data_ex[1] != base64_encode(az09($cmdparams['p'])))
{
	$response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Invalid password for user "'.az09($cmdparams['u']).'".','format' => 'line'),
                    )
            );
    return; 
}

$response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Instance set to "'.az09($cmdparams['u']).'" user successfully.','format' => 'line'),
                    )
            );