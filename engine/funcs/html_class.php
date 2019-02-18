<?php

class HTMLClass
{
    private $_startpage = '';
    private $_head = '';
    private $_endpage = '';
    private $_cssPaths = array();
    private $_jsPaths = array();

    private $_title = 'Default::NoTitle'; //Secret Facility Command Console

    /**
     * HTMLClass::__construct()
     * Nothing to do here.
     * @return void
     */
    public function __construct()
    {

    }

    function SetTitle($title)
    {
        $this->_title = $title;
    }

    public function AppendToHead($html)
    {
        $this->_head .= $html;
    }

    function AddJSFile($jsFilePath)
    {
        $this->_jsPaths[md5($jsFilePath)] = $jsFilePath;
    }

    function AddCSSFile($cssFilePath,$media = 'screen')
    {
        $this->_cssPaths[md5($cssFilePath.'_'.$media)] = array('path' => $cssFilePath,'media' => $media);
    }


    public function SetStartPageHTML($html = '',$append = true)
    {
        if ($append)
            $this->_startpage .= $html;
        else
            $this->_startpage = $html;
    }

    public function SetEndPageHTML($html = '',$append = true)
    {
        if ($append)
            $this->_endpage .= $html;
        else
            $this->_endpage = $html;
    }


    public function El_RenderAlert($title,$bodyHTML = '',$icon = 'lock')
    {
        $r = '<div class="mf-alert">';
        $r .= '<div class="mf-alert--title"><div class="mf-icon"><i class="fa fa-'.$icon.'"></i></div>'.$title.'</div>';
        $r .= '<div class="mf-alert--body">'.$bodyHTML.'</div>';
        $r .= '</div>';
        echo $r;
    }

    /**
    * Includes template file, $params are extracted to variables.
    */
    public function getTpl($_template,$params = [])
    {
      $_tplPath =  PATHROOT.'app/mainframe/template/'.$_template.'.phtml';
      if (is_file($_tplPath))
      {
        extract($params);
        ob_start();
        include $_tplPath;
        return ob_get_clean();
      }

      return '[INVALID TEMPLATE PATH: '.$_tplPath.']';
    }



    public function RenderHeader()
    {
        echo '<!DOCTYPE html><html lang="en"><head>'."\n";
        echo '<title>'.$this->_title.'</title>'."\n";
        foreach($this->_jsPaths as $path)
        {
            echo '<script src="'.$path.'"></script>'."\n";
        }

        foreach($this->_cssPaths as $pathData)
        {
            echo '<link rel="stylesheet" href="'.$pathData['path'].'" type="text/css" media="'.$pathData['media'].'" />'."\n";
        }
        echo $this->_head;
        echo '</head><body>';
        echo $this->_startpage;
    }

    public function RenderFooter()
    {
        echo $this->_endpage;
        echo '</body></html>';
    }
}
