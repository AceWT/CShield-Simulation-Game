<?php


class Console_Console
{
    private $_instanceID;
    private $_lines = array();
    private $_steptracker = array(0);//local step tracker
    
    public function __construct($instanceID)
    {
        if (empty($instanceID))
        {
            die('Unable to start console - instance ID not provided');
        }
        $this->_instanceID = $instanceID;
    }
    
    
    public function WriteLine($line,$format = 'line',$color = 'lime')
    {
        $this->_lines[] = array('timestamp' => time(), 'line' => $line, 'format' => $format, 'color' => $color);
    }
    
    
    /**
     * Console_Console::Render()
     * Outputs console lines.
     * @return void
     */
    public function Render($return = false)
    {
        $consoleID = $this->_instanceID;
        $r = '<div id="console">';
        foreach($this->_lines as $lineData)
        {
            if ($lineData['format'] == 'line')
                $r .= '<div><span style="color:#ccffcc">Console R'.$consoleID.'&gt;</span> <span style="color:'.$lineData['color'].'">'.$lineData['line'].'</span></div>'."\n";
            else
                $r .= '<div>'.$lineData['line'].'</div>';
        }
        
        $r .= '</div>';
        
        //add input line
        $r .= '
            <div><span style="color:#ccffcc">Console R'.$consoleID.'&gt;</span> 
            <form id="commandinput" method="POST"><input type="text" placeholder=" _" /><input type="hidden" id="consoleid" value="'.$consoleID.'" /></form>
            <span class="command-sending">Sending...</span>
            </div>';
        
        
        
        if ($return) return $r;
        echo $r;
    }
    
    /**
     * Console_Console::Line()
     * 
     * @param string $txt - text - if format is raw set content in div with style color of your choice
     * @param string $format - line (default), raw
     * @param integer $step - 1 (be consistant 1 to infinity)
     * @param integer $timeout - 0 fo not timeout or 1000 for 1 second
     * @return array - formatted array for interpreter to js
     */
    public function Line($txt = 'TXT',$format = 'line',$step = 1, $timeout = 0)
    {
        if (in_array(($step-1),$this->_steptracker))
            $this->_steptracker[] = $step;
        else
            return array( 'step' => 1, 'v' => 'ERR_INCONSISTENT_STEPS', 'format' => 'line');
        return array( 'step' => $step, 'v' => $txt, 'format' => $format, 'timeout' => $timeout);
    }
}