<?php

sleep(2);
$status_online = '<span style="color:lime">ONLINE</span>';
$status_offline = '<span style="color:red">OFFLINE</span>';
$status_unknown = '<span style="color:gray">Unknown</span>';
$mainframestatus = ($Instance->Game->GetSetting('mainframestatus') == 1) ? $status_online:$status_offline;
$response = array( //format types: line, raw
                'data' => array(
                    array('v' => '<div style="color:white">System:</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad('CONSOLE ',20,'-').' '.$status_online.'</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad(' - RAM ',21,'-').' '.rand(22,25).'MB/128MB</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad(' - CPU ',21,'-').' '.rand(10,80).'%</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad(' - Latency ',21,'-').' '.rand(100,250).'ms</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad('MAINFRAME ',20,'-').' '.$mainframestatus.'</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad(' - RAM ',21,'-').' '.$status_unknown.'</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">'.str_pad(' - SENSORS ',21,'-').' '.$status_unknown.'</div>','format' => 'raw'),
                    array('v' => '<div style="color:white">Power:</div>','format' => 'raw'),
                )
            );
//get generators

$generators = $Instance->Game->GetData('generators');

if ($generators)
{
    foreach($generators as $k => $v)
    {
        $statuslbl = ($v['status'] == 1) ? $status_online:$status_offline;
        $response['data'][] = array('v' => '<div style="color:white">'.str_pad($v['name'].' ',20,'-').' '.$statuslbl.'</div>','format' => 'raw');
    }
}
