<?php

$HTML->SetStartPageHTML('<div class="mf mf-login">',false);
$HTML->SetEndPageHTML('</div>',false);


$alertHtml= $HTML->getTpl('login/loginform');

$HTML->El_RenderAlert('Access denied',$alertHtml);
?>
