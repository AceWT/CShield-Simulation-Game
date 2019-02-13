<?php

//print_r($cmdparams);

if (!isset($cmdparams['path']))
{
   $response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'No path specified. Use run -path [path]','format' => 'line'),
                    )
            );
   return; 
}
$console_ls_harddrivepath = PATHROOT. 'app/console/game/harddrive/'.$cmdparams['path'];

if (is_dir($console_ls_harddrivepath))
{
    
   $response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Unable to run path, it is directory.','format' => 'line'),
                    )
            );
   return;
}


$programPath = $console_ls_harddrivepath.'.run.txt';
if (!is_file($programPath))
{
    $response = array( //format types: line, raw
                'data' => array(
                    array('v' => 'Unable to find program at specified path. ','format' => 'line'),
                    )
            );
   return;
}

//$programContents = file_get_contents($programPath);
$programResponse = false;

$response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<div style="color:white">RUN::</div>','format' => 'raw'),
                    //array('v' => '<div style="color:white">'.$programResponse.'</div>','format' => 'raw'),
                    )
            );
            
include $programPath; //run php code contained in program. - $programResponse variable should be set.
