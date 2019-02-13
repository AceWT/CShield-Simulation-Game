<?php

$HTML->SetStartPageHTML('<div class="mf mf-login">',false);
$HTML->SetEndPageHTML('</div>',false);


$alerttxt = '';

$HTML->El_RenderAlert('Access denied',$alerttxt);
?>