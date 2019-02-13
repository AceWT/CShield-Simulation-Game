<?php

$Instance->DeleteCurrentInstance();

$response = array( //format types: line, raw
                'redirect' => '/',
                'data' => array(
                    array('v' => 'Instance deleted.','format' => 'line'),
                )
);
