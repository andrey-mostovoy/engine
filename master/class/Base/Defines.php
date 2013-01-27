<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.2
 * @since		Version 1.1
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

require_once 'configs/Config.php';

define('DS', DIRECTORY_SEPARATOR);

/**
 * class BaseDefines
 * containing base constants and static variables for project
 * @package		Base
 * @author		amostovoy
 */
class BaseDefines extends Config
{
    /**
     * Nubmer of seconds in one hour (for different calculations)
     */
    const SECONDS_IN_HOUR = 3600;
    /**
     * Nubmer of seconds in one day (for different calculations)
     */
    public static $seconds_in_day = null;
    /**
     * regular pattern for check email address
     */
    const EMAIL_PATTERN = "/^[a-z0-9\-\._]+@([a-z0-9\-]+\.)+[a-z]{2,4}$/i";  //'/^([0-9a-zA-Z]([-\.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/'
    /**
     * Live time for permanent cookies (currency, language, etc...)
     * This var used to set up such functionality as "remember me"
     */
    public static $cookie_alive_time = null;
    /**
     * Session live time. Also used as number of seconds 
     * after last session update, while user considered as online
     */
    public static $session_alive_time = null;
    /**
     * cookie set path
     * @var string
     */
    public static $cookie_path = '/';
    /**
     * calculate static variables
     */
    public static function selfCalculate()
    {
        self::$seconds_in_day = self::SECONDS_IN_HOUR * 24;
    }
}
/* End of file Defines.php */
/* Location: ./class/Base/Defines.php */
?>