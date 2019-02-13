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


$filePath = $console_ls_harddrivepath.'.txt';
if (!is_file($filePath))
{
    $filePath = $console_ls_harddrivepath;
    if (!is_file($filePath))
    {
        $response = array( //format types: line, raw
                    'data' => array(
                        array('v' => 'Unable to find file at specified path.','format' => 'line'),
                        )
                );
       return;
    }
}

$fileContents = file_get_contents($filePath);


$response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<div style="color:white">OPEN::</div>','format' => 'raw'),
                    array('v' => '<div style="color:yellow">File start ****</div>','format' => 'raw'),
                    array('v' => '<div style="color:gray">'.nl2br($fileContents).'</div>','format' => 'raw'),
                    array('v' => '<div style="color:yellow">File end ******</div>','format' => 'raw'),
                    )
            );