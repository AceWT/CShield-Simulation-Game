<?php

$console_ls_harddrivepath = PATHROOT. 'app/console/game/harddrive/';

$responseData = array(
                    array('v' => '<div>BKT4 (backup terminal 4):</div>','format' => 'raw')
                );
function console_ls_buildfilesystem($path,$level = 0,$currpath = '')
{
    global $responseData;
    $res = scandir($path);
    
    foreach($res as $k => $v)
    {
        if ($v == '.' || $v == '..')
            continue;
        $isDir = (is_dir($path.$v));
        
        $prefix = '';
        for ($x = 0; $x <= $level; $x++) {
            if ($x == 0)
                $prefix .= '|';
            else
                $prefix .= '-';
        }
        $attr = ($isDir) ? ' style="color:#66d9ff"':' style="color:#fff"';
        if (!$isDir)
        {
            $lbl = $v;
            if (endsWith($v,'.run.txt'))
            {
                $programName = preg_replace('/.run.txt$/', '', $v);
                $lbl = '<span style="color:gray">[PROGRAM]</span> <a onclick="return SetCommand(\'run -path '.ltrim($currpath.'/','/').$programName.'\')" href="#">'.$programName.'.run</a>';
            }
            else
            {
                $fileName = preg_replace('/.txt$/', '', $v);
                $lbl = '<a onclick="return SetCommand(\'open -path '.ltrim($currpath.'/','/').$fileName.'\')" href="#">'.$fileName.'</a>';
            }
            $responseData[] = array('v' => '<div'.$attr.'>'.$prefix.' '.$lbl.'</div>','format' => 'raw');
        }  
        else
            $responseData[] = array('v' => '<div'.$attr.'>'.$prefix.' '.$v.'</div>','format' => 'raw');
        if ($isDir)
        {
            //recursiveness
            console_ls_buildfilesystem($path.$v.'/',($level+1),$currpath.'/'.$v);
        }
    }
}


console_ls_buildfilesystem($console_ls_harddrivepath);

//print_r($console_ls_dirstructurearray);

$response = array( //format types: line, raw
                'data' => $responseData
            );
/*
$response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<div>BKT4 (backup terminal 4):</div>','format' => 'raw'),
                    array('v' => '<div>| bin</div>','format' => 'raw'),
                    array('v' => '<div>|-| data</div>','format' => 'raw'),
                    array('v' => '<div>| hosts</div>','format' => 'raw'),
                    array('v' => '<div>| messages</div>','format' => 'raw'),
                )
            );
*/