<?php

$listinstances_instances = $db->select('instances', array('id','timeactive'),
    array(
        'AND' => array(
            'timeactive[>]' => toMysqlDate((time()-600)),
        ),
        
        'LIMIT' => 100
    )
);

$response = array( //format types: line, raw
                'data' => array(
                array('v' => '<div style="color:white">Listing all active instances:</div>','format' => 'raw'),
                )
);


//print_r($listinstances_instances);
foreach($listinstances_instances as $k => $v)
{
    $response['data'][] = array('v' => '<div style="color:white">[ '.$v['timeactive'].' ] Instance #'.$v['id'].'</div>','format' => 'raw');
}