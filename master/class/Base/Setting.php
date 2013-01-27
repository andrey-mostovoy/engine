<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2011, Qualium-Systems, Inc.
 * @version		1
 * @since		Version 1.4
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/
Loader::loadClass('Base_SettingStorage');

/**
 * class Setting
 * 
 * @package		Base
 * @author		amostovoy
 */
class Setting extends SettingStorage
{
    /**
     * Major zonelist by time offset
     * @var array
     */
    private static $timezonelist = null;
    /**
     * Setting class instance
     * @var /Setting
     */
    private static $instance = null;
    /**
     * class constructor. Run global checks and set global settings.
     * Create container instanse for SettingStorage
     * and set Supplier for common settings from db
     */
    private function __construct()
    {
        $this->checkLive();
        $this->setLocale();
        $this->setDefaultTimezone();
        $this->setPhpIniDirectives();
		$this->disableMagicQuotes();
        
        $this->_container = new Container();
        
        $this->appendSuppliers(array(
           'db' => 'SettingCommon',
        ));
    }
    /**
     * get class instance. Singleton.
     * 
     * @param string $section (optional d:'db') used for \SettingStorage
     * to get setting. @see \SettingStorage
     * @return Setting 
     */
    public static function getInstance($section = 'db')
    {
        self::$instance === null and self::$instance = new self();
        return self::$instance->section($section);
    }
    
    /**
     * Set Locale if specified special constants
     */
    public function setLocale()
    {
        if (defined('Defines::SITE_LOCALE'))
        {
//            setlocale(LC_ALL, Defines::SITE_LOCALE.(defined('Defines::SITE_CHARSET')?'.'.Defines::SITE_CHARSET:''));
            if(!setlocale(LC_ALL, Defines::SITE_LOCALE))
            {
                trigger_error('Local hasn\'t been set!');
            }
        }
    }
    /**
     * Set Timezone if specified special constants
     */
    private function setDefaultTimezone()
    {
        if(defined('Defines::TIMEZONE_AUTOMAT') && Defines::TIMEZONE_AUTOMAT)
        {
            $this->setTimezone($this->getLocalTimezone());
        }
        elseif(defined('Defines::TIMEZONE_OFFSET'))
        {
            return $this->setTimezoneByOffset(Defines::TIMEZONE_OFFSET);
        }
        elseif (defined('Defines::TIMEZONE'))
        {
            return $this->setTimezone(Defines::TIMEZONE);
        }
        else
        {
            return $this->setTimezone('UTC');
        }
    }
    /**
     * Set timezone by offset
     * @param int $offset offset in seconds or in hours(-12 to 12)
     * @return boolean true on success
     */
    public function setTimezoneByOffset($offset)
    {
        if($offset < -24 || $offset > 24)
        {
            $offset = $offset/(60*60);
        }
        $this->setTimezoneList();
        $index = array_keys(self::$timezonelist, floatval($offset));
        if(sizeof($index)!=1)
            return false;
        return $this->setTimezone($index[0]);
    }
    
    /**
     * Set default timezone for all date functions on the script
     * @param string $tz timezone identifyer. Continent\Location\Sublocation
     * @return boolean true on success
     */
    public function setTimezone($tz)
    {
        if(!date_default_timezone_set($tz))
        {
            trigger_error('Timezone '.$tz.' hasn\'t been set!');
            return false;
        }
        return true;
    }
    
    /**
     * check settings for live site. Turn debug mode on or off
     */
    private function checkLive()
    {
        if (Config::DEBUG || Defines::HARD_DEBUG)
        {
            ini_set('display_errors', 1);
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_startup_errors', 1);
        }
		else
        {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors ', 0);
        }
    }
    
    /**
     * Set php ini directives set in Config
     */
    private function setPhpIniDirectives()
    {
        if(isset(Config::$php_ini) && !empty(Config::$php_ini))
        {
            foreach(Config::$php_ini as $directive => $value)
            {
                ini_set($directive, $value);
            }
        }
    }
	/**
	 * Disabling magic quotes at runtime
	 */
	private function disableMagicQuotes()
	{
		if (get_magic_quotes_gpc())
		{
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
			while (list($key, $val) = each($process))
			{
				foreach ($val as $k => $v)
				{
					unset($process[$key][$k]);
					if (is_array($v))
					{
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					}
					else
					{
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
			unset($process);
		}
	}
    /**
     * Try to get current timezone
     * @return boolean|string return timezone abbreviation or false on fail
     */
    private function getLocalTimezone()
    {
        $iTime = @time();
        $arr = @localtime($iTime);
        $arr[5] += 1900; 
        $arr[4]++;
        $iTztime = @gmmktime($arr[2], $arr[1], $arr[0], $arr[4], $arr[3], $arr[5]);
        $offset = floatval(($iTztime-$iTime)/(60*60));
        $this->setTimezoneList();
        $index = array_keys(self::$timezonelist, $offset);
        if(sizeof($index)!=1)
            return false;
        return $index[0];
    }
    /**
     * Set major time zones by its offset 
     */
    private function setTimezoneList()
    {
        self::$timezonelist = array(
            'Pacific/Kwajalein'=>-12,
            'Pacific/Samoa'=>-11,
            'Pacific/Honolulu'=>-10,
            'America/Juneau'=>-9,
            'America/Los_Angeles'=>-8,
            'America/Denver'=>-7,
            'America/Mexico_City'=>-6,
            'America/New_York'=>-5,
            'America/Caracas'=>-4,
//            'America/Caracas' => -4.30,
//            'America/Halifax' => -4.00,
//            'America/St_Johns' => -3.30,
            'America/St_Johns' =>-3.5,
            'America/Argentina/Buenos_Aires'=>-3,
            'Atlantic/South_Georgia'=>-2,
            'Atlantic/Azores'=>-1,
            'Europe/London'=>0,
            'Europe/Paris'=>1,
            'Europe/Helsinki'=>2,
            'Europe/Moscow'=>3,
//            'Asia/Tehran' => 3.30,
            'Asia/Tehran'=>3.5,
            'Asia/Baku'=>4,
            'Asia/Kabul'=>4.5,
//            'Asia/Yekaterinburg' => 5.00,
            'Asia/Karachi'=>5,
            'Asia/Calcutta'=>5.5,
//            'Asia/Kolkata' => 5.30,
//            'Asia/Katmandu' => 5.45,
            'Asia/Colombo'=>6,
//            'Asia/Dhaka' => 6.00,
//            'Asia/Rangoon' => 6.30,
//            'Asia/Krasnoyarsk' => 7.00,
            'Asia/Bangkok'=>7,
            'Asia/Singapore'=>8,
            'Asia/Tokyo'=>9,
//            'Australia/Darwin' => 9.30,
            'Australia/Darwin'=>9.5,
//            'Pacific/Guam'=>10,
            'Australia/Canberra' => 10.00,
            'Asia/Magadan'=>11,
            'Asia/Kamchatka'=>12,
//            'Pacific/Fiji' => 12.00,
//            'Pacific/Tongatapu' => 13.00,
        );
    }
}
/* End of file Setting.php */
/* Location: ./class/Base/Setting.php */
?>