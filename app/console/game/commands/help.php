<?php

$help_commanddir = PATHROOT.'app/console/game/commands/';
$help_dirlist = scandir($help_commanddir);
//print_R($help_dirlist);
/*
Array
(
    [0] => .
    [1] => ..
    [2] => about.help
    [3] => about.php
    [4] => help.help
    [5] => help.php
    [6] => listinstances.help
    [7] => listinstances.php
)
*/
$response = array( //format types: line, raw
                'data' => array(
                array('v' => '<div style="color:white">Listing all commands:</div>','format' => 'raw'),
                )
);


foreach($help_dirlist as $filename)
{
    // print_r($filename);
    if (endsWith($filename,'.help'))
    {
        $helptxt = file_get_contents( $help_commanddir.$filename );
        $filenameex = explode('.',$filename);
        $response['data'][] = array('v' => '<div style="color:white">'.str_pad(strtoupper($filenameex[0]).' ',20,'-').' '.$helptxt.'</div>','format' => 'raw');
    }
}

/*
$response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<div>'.str_pad('ABOUT ',20,'-').' Displays general information.</div>','format' => 'raw'),
                    array('v' => '<div>'.str_pad('HELP ',20,'-').' Displays list of available commands.</div>','format' => 'raw'),
                )
            );*/