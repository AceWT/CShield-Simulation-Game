<?php

define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default

class SessionInstanceClass
{
    public $id;
    public $isloaded = false;
    public $Game;
    private $flash = [];//stores flash messages
    /**
     * SessionInstanceClass::__construct()
     * Load or create new session from cookie.
     * @return void
     */
    public function __construct()
    {
        global $db,$config;

        $cookiename = $config['session_cookiename'];
        if (isset($_COOKIE[$cookiename]))
        {

            //load from cookie
            $data = $this->_loadInstanceDataFromSessionID($_COOKIE[$cookiename]);
            if (!$data)
            {
                //create new
                $this->_createSession();
            }
            else
            {
                //log visit
                $db->update('instances', array(
                        'timeactive' => toMysqlDate(time()),
                    ),
                    array(
                        'id[=]' => $data['id'],
                    )
                );
            }
        }
        else
        {
            if(isset($_SESSION['cshield_'.$config['session_cookiename']]))
            {
                $data = $this->_loadInstanceDataFromSessionID($_SESSION['cshield_'.$config['session_cookiename']]);
                if (!$data)
                {
                    //create new
                    $this->_createSession();
                }
            }
            else
                //create new
                $this->_createSession();
        }


        if ($this->isloaded)
        {
            $this->Game = new GameObjectClass($this->id);
        }
    }

    public function __destruct()
    {
      if(!isset($_SESSION['cshield_flash']))
        $_SESSION['cshield_flash'] = $this->flash;
      else
        $_SESSION['cshield_flash'] = $this->flash;
    }

    public function DeleteCurrentInstance()
    {
        global $db;
        if (!$this->isloaded) return;

        $db->delete('instances',
            array(
                'id[=]' => $this->id
            )
        );
    }

    private function _loadInstanceDataFromSessionID($sessionID)
    {

        $sessionID = az09($sessionID);
        global $db,$config;
        $r = $db->get('instances','*',
            array('sessionID[=]' => $sessionID)
        );
        $this->isloaded = (!$r) ? false:true;
        if ($this->isloaded)
        {
            $this->id = $r['id'];
            if(!isset($_SESSION['cshield_'.$config['session_cookiename']]))
                $_SESSION['cshield_'.$config['session_cookiename']] = $sessionID;
            if (!isset($_COOKIE[$config['session_cookiename']]))
                 setcookie($config['session_cookiename'], $sessionID, time()+COOKIE_EXPIRE, '/',$config['session_cookiedomain']);
        }

        return $r;
    }

    private function _loadInstanceDataFromInstanceID($id)
    {
        $id = to09($id);
        global $db;
        $r = $db->get('instances','*',
            array('id[=]' => $id)
        );

        $this->isloaded = (!$r) ? false:true;
        if ($this->isloaded)
            $this->id = $r['id'];
        return $r;
    }

    private function _createSession()
    {
        global $db,$config;
        $newSessionID = md5(time().rand(1,9999));
        $instanceID = $db->insert('instances',
            array(
                'sessionID' => $newSessionID,
                'timestarted' => toMysqlDate(time()),
                'timeactive' => toMysqlDate(time()),
                'ip' => GetIP(),
                'useragent' => isset($_SERVER['HTTP_USER_AGENT']) ? toSafeString( $_SERVER['HTTP_USER_AGENT'] ) : NULL
            )
        );
        if (empty($instanceID)) die('Unable to create instance.');
        $this->id = $instanceID;
        setcookie($config['session_cookiename'], $newSessionID, time()+COOKIE_EXPIRE, '/',$config['session_cookiedomain']);
        $_SESSION['cshield_'.$config['session_cookiename']] = $newSessionID;

        //load session
        $this->_loadInstanceDataFromInstanceID($this->id);

    }

    public function flash($section,$message)
    {
      if (!isset($this->flash[$section]))
        $this->flash[$section] = [];
      $this->flash[$section][] = $message;
    }

    /**
    * Get flash messages from session.
    */
    public function getFlash()
    {
      if(isset($_SESSION['cshield_flash']))
      {
        $flash = $_SESSION['cshield_flash'];
        unset($_SESSION['cshield_flash']);
        return $flash;
      }

      return [];
    }
}
