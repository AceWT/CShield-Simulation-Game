<?php
#wrapper
$HTML->SetStartPageHTML('<div class="mf mf-login">',false);
$HTML->SetEndPageHTML('</div>',false);

$HTML->SetEndPageHTML('<div class="sysinfo">SYSINFO: '.$config['game_corptitle_long'].', '.$config['game_console_version'].' <a href="/">Open console</a></div>');
#toastr
$HTML->SetEndPageHTML($HTML->getTpl('toastr',['messages' => $Instance->getFlash()]));


if (isset($_POST) && isset($_POST['username']) && isset($_POST['password']))
{
  $username = $_POST['username'];
  $password = $_POST['password'];

  $usersPath = PATHHDD.'bin/users/'.az09($username).'.txt';
  if(is_file($usersPath))
  {
    $Instance->flash('success','Logged in');
  }
  else {
    $Instance->flash('error','Invalid credentials');
    redirect('/mainframe/login');
  }
}
$alertHtml= $HTML->getTpl('login/loginform');
$HTML->El_RenderAlert('Access denied',$alertHtml);
