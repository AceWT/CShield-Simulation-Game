<?php



/**
 * GameObjectClass
 * Init from Instance Class.
 * @package 
 * @author CD
 * @copyright 2017
 * @version 1.0.0
 * @access public
 */
class GameObjectClass
{
    public $id = false;
    
    public function __construct($id)
    {
        if (empty($id))
        {
            die('Unable to init GO Class without instance ID.');
        }
        $this->id = $id;
    }
    
    
    public function GetModelData($tableNameSimple,$WHERE = array(),$LIMIT = 100)
    {
        return $this->_getDataFromTable($tableNameSimple,$WHERE,$LIMIT,'model');
    }
    
    public function GetData($tableNameSimple,$WHERE = array(),$LIMIT = 100,$autoinsertfrommodel = true)
    {
        $r = $this->_getDataFromTable($tableNameSimple,$WHERE,$LIMIT,'game');
        
        if (!$r && $autoinsertfrommodel)
        {
            $this->_copyModelDataToInstanceData($tableNameSimple,$WHERE,$LIMIT,'game');
            $r = $this->_getDataFromTable($tableNameSimple,$WHERE,$LIMIT,'game');
        }
        return $r;
    }
	
	/**
	 * GameObjectClass::GetSetting()
	 * Inserts default values automatically.
	 * @param mixed $name
	 * @return
	 */
	public function GetSetting($name)
	{
		global $db;
		
		$r = $db->get('game_settings','settingvalue',
			array(
				'AND' => array(
					'settingname' => $name,
					'instanceID' => $this->id
				)
			)
		);
        if ($r === false)
        {
            //try to get from model
            $origSettingData = $this->_getSettingDataFromModel($name);
           
            if ($origSettingData === false)
                return false;
                
            $origSettingData['instanceID'] = $this->id;
            $origSettingData['referenceID'] = $origSettingData['id'];
            unset($origSettingData['id']);
            $insert = $db->insert('game_settings',
    			$origSettingData
    		);
            //echo $db->last_query();
            //var_dump($db->error());
            return $origSettingData['settingvalue'];
        }
        
        return $r;
	}
	
	public function SetSetting($name,$value)
	{
		global $db;
        $currval = $this->GetSetting($name); //this will ensure value exist.
        if ($currval === false) return false;
		return $db->update('game_settings',
			array('settingvalue' => $value),
			array(
				'AND' => array(
					'settingname' => $name,
					'instanceID' => $this->id
				)
			)
		);
	}
	
	public function GetSettings(array $arrayofsettings)
	{
		global $db;
		
		return $db->select('game_settings','settingvalue',
			array(
				'AND' => array(
					'settingname' => $arrayofsettings,
					'instanceID' => $this->id
				)
			)
		);
	}
    
    
    private function _getSettingDataFromModel($name)
    {
   	    global $db;
        
		return $db->get('model_settings','*',
			array(
				'AND' => array(
					'settingname' => $name
				)
			)
		);
    }
    
    public function _getSettingsDataFromModel(array $arrayofsettings)
	{
		global $db;
		
		return $db->select('model_settings','*',
			array(
				'AND' => array(
					'settingname' => $arrayofsettings
				)
			)
		);
	}
    //PRIVATE
    
    private function _copyModelDataToInstanceData($tableNameSimple,$WHERE = array(),$LIMIT = 100)
    {
        $r = $this->GetModelData($tableNameSimple,$WHERE,1000);
        if ($r)
        {
            global $db;
            //copy data
            //print_r($r);
            foreach($r as $rowID => $row)
            {
                $r[$rowID]['instanceID'] = $this->id;
                $r[$rowID]['referenceID'] = $row['id']; //for info
                unset($r[$rowID]['id']);
            }
            $db->insert('game_'.$tableNameSimple,$r); //multiinsert
            return true;
        }
        return false;
    }
    
    private function _getDataFromTable($tableNameSimple,$WHERE = array(),$LIMIT = 100,$prefix = 'game')
    {
        global $db;
        if (empty($WHERE))
            $WHERE = array( 'LIMIT' => $LIMIT );
        else
            $WHERE['LIMIT'] = $LIMIT;
            
        if ($prefix == 'game')
        {
            if (!isset($WHERE['AND']))
                $WHERE['AND'] = array();
            $WHERE['AND']['instanceID'] = $this->id;
        }
		
        $r = $db->select($prefix.'_'.$tableNameSimple,'*',
            $WHERE
        );
        //echo $db->last_query();
		if ($prefix == 'game')
		{
			$error = $db->error();
			if ($error[0] == '42S02') //whoops table game_$tableNameSimple does not exist. We will create it from model.
			{
				$db->query('CREATE TABLE game_'.$tableNameSimple.' LIKE model_'.$tableNameSimple.'');
				$createTableErr = $db->error();
				if ($createTableErr[0] == '00000') //no errors
				{
					
					//add column referenceID
					//ADD COLUMN `referenceID` int(11) NULL AFTER `id`;
					$db->query('ALTER TABLE `game_'.$tableNameSimple.'` ADD COLUMN `referenceID` int(11) NULL AFTER `id`');
					
					//ALTER TABLE `game_settings` ADD CONSTRAINT `settings_ref` FOREIGN KEY (`referenceID`) REFERENCES `model_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
					$db->query('ALTER TABLE `game_'.$tableNameSimple.'` ADD CONSTRAINT `'.$tableNameSimple.'_ref` FOREIGN KEY (`referenceID`) REFERENCES `model_'.$tableNameSimple.'` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
					//echo $db->last_query();
					//var_dump($db->error());
					//die('test');
					//add column instanceID
					//ADD COLUMN `instanceID`  int(11) NOT NULL AFTER `id`;
					$db->query('ALTER TABLE `game_'.$tableNameSimple.'` ADD COLUMN `instanceID`  int(11) NOT NULL AFTER `id`');
					//ALTER TABLE `game_settings` ADD CONSTRAINT `settings_instanceid` FOREIGN KEY (`instanceID`) REFERENCES `instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
					$db->query('ALTER TABLE `game_'.$tableNameSimple.'` ADD CONSTRAINT `'.$tableNameSimple.'_instanceid` FOREIGN KEY (`instanceID`) REFERENCES `instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
				}
				//echo $db->last_query();
				//var_dump($db->error());
			}	
		}
		
        return $r;
    }
}