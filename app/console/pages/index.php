<?php

//run command
if (isset($_POST) && isset($_POST['cmd']))
{
    header('Content-Type: application/json');
    
    $cmdraw = toCommandText($_POST['cmd']);
    $step = (isset($_POST['step'])) ? to09($_POST['step']) : 1;
    $cmdex = explode(' -',$cmdraw);
    $cmd = $cmdex[0];
    $cmdparams = array();
    if (isset($cmdex[1]))
    {
        foreach($cmdex as $k => $v)
        {
            if ($k == 0) continue;
            $tmp = explode(' ',$v);
            $tmp2 = $tmp; unset($tmp2[0]);
            $cmdparams[$tmp[0]] = implode(' ',$tmp2);
        }
    }
    unset($cmdex);
    //sleep(1);
    //example - invoke communication error:
    //header("HTTP/1.1 500 Internal Server Error");exit;
    
    
    
    if (is_file(PATHROOT.'app/console/game/commands/'.strtolower($cmd).'.php')) //params are in: $cmdparams
    {
        include PATHROOT.'app/console/game/commands/'.strtolower($cmd).'.php';
        if (!isset($response))
        {
            $response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<span style="color:red">No response.</span>','format' => 'line'),
                ),
                'islast' => 1
            );
        }
        $isLastStep = true;
        $sleep = 0;
        foreach($response['data'] as $responseKey => $responseLine)
        {
            if (!isset($responseLine['step'])) $response['data'][$responseKey]['step'] = 1;
            if ($response['data'][$responseKey]['step'] == ($step+1))
                $isLastStep = false;
            if ($response['data'][$responseKey]['step'] != $step)
            {
                unset($response['data'][$responseKey]);
                continue;
            }
            if (isset($response['data'][$responseKey]['timeout']))
                $sleep = $sleep+$response['data'][$responseKey]['timeout'];
            unset($response['data'][$responseKey]['timeout']); //hide timeout in ajax
        }
        if ($isLastStep)
            $response['islast'] = 1;
        if ($sleep > 0)
            usleep((int)$sleep*1000);
        echo json_encode($response);
        exit;
    }
    else
    {
        $response = array( //format types: line, raw
            'data' => array(
                array('v' => 'Invalid command, type help for more info.','format' => 'line'),
            ),
            'islast' => 1
        );
        echo json_encode($response);
        exit;
    }
    
    
    
    //example:
    /*
    $response = array( //format types: line, raw
        'data' => array(
            array('v' => 'test','format' => 'line'),
            array('v' => 'test2','format' => 'raw'),
            array('v' => 'test3','format' => 'raw'),
            array('v' => 'test4','format' => 'line'),
        )
    );
    
    echo json_encode($response);
    */
    exit;
}

$HTML->SetTitle('CSHIELD::Console');

$Console->WriteLine('** '.$config['game_corptitle_short'].' COMMAND CONSOLE **','raw');
$Console->WriteLine($config['game_console_version'],'raw');
$Console->WriteLine('&nbsp;','raw');
$Console->WriteLine('[ Broadcast:SYSTEM ] Lockdown complete','line','yellow');
$Console->WriteLine('[ Broadcast:SYSTEM ] Emergency Shutdown Initiated','line','yellow');
$Console->WriteLine('[ Broadcast:SYSTEM ] Emergency Shutdown Complete','line','yellow');
$Console->WriteLine('Remote instance '.$Instance->id.' created');
if ($Instance->Game->GetSetting('mainframestatus') == 0)
	$Console->WriteLine('Starting MAINFRAME Session... <span style="color:red">Unable to connect</span>','line');
else
{
	$Console->WriteLine('Mainframe ready.','line');
	$Console->WriteLine('Type "start" to start mainframe...','line');
}
	
//$Console->WriteLine('Connection with MAINFRAME lost...','line','red');

//if this stage is passed then output: "Starting MAINFRAME Web Interface..."
//$Console->WriteLine('Starting MAINFRAME Web Interface...','line');
$Console->Render();