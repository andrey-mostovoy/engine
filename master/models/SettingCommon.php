<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */
Loader::loadModel('Content', false);
/**
 * class modelSettingCommon
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelSettingCommon extends modelContent implements SettingSupplier
{
	protected function setName()
    {
        return self::$tbl_setting;
    }
    protected function setValidationRules()
    {
        return array();
    }
    /**
     * Get setting. If setting had already taken
     * return class variable instead of db query
     * @param string $name
     * @return string
     */
    public function getSetting($name)
    {
        if(isset($this->$name))
            return $this->$name;
        
        return $this->$name = $this->getOne('value', array('name'=>$name));
    }
    /**
     * load setting from db and add to SettingStorage container
     */
    public function settingLoad()
    {
        $r = App::setting()->getRequest();
        App::setting('db')->{$r[0]} = $this->getSetting($r[0]);
    }
}

/* End of file SettingCommon.php */
/* Location: ./models/SettingCommon.php */
?>