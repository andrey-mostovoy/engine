<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.12
 * @since		Version 1.2
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base_Defines');

/**
 * class Defines
 * containing constants and static variables for project
 * @package		Base
 * @author		amostovoy
 */
class Defines extends BaseDefines
{
    /**
     * Indicate status in project.
     * Use to show or hide some functionality on
     * live or dev project status 
     */
    const DEV = 1;
    /**
     * Debug queries 
     */
    const SHOW_QUERY = 0;
    /**
     * catch exceptions if use Undefined var or other coding errors
     */
    const HARD_DEBUG = 0;
    /**
     * site title delimiter
     */
    const TITLE_DELIMITER = ' - ';
    /**
     * Set timezone automatic depend on server location 
     */
    const TIMEZONE_AUTOMAT = false;
    /**
     * Default Timezone. List on http://www.php.net/manual/en/timezones.php
     */
//    const TIMEZONE = 'Europe/Kiev';
    /**
     * Default timezone by offset in hour or in seconds
     */
//    const TIMEZONE_OFFSET = '3';
    /**
     * Locale
     */
//    const SITE_LOCALE = "deu_deu";
    /**
     * Locale Charset
     */
//    const SITE_CHARSET = "UTF-8";
    /**
     * Locale HTML Charset
     */
//    const SITE_HTML_CHARSET = "iso-8859-1";

    

    /************************************
     *    place your constants below    *
     ***********************************/
    /**
     * date formats for sql queries
     */
    const SQL_DATE_FORMAT_VIEW = '%d-%m-%Y';
    const SQL_DATE_FORMAT = '%Y-%m-%d';
    const SQL_DATE_FORMAT_DATETIME = '%Y-%m-%d %H:%i:%s';
    const SQL_DATE_FORMAT_DEP_DATE_NAME = '%M %Y';
    const SQL_DATE_FORMAT_DEP_DATE_VAL = '%Y-%m';
    /**
     * date formats for php
     */
    const PHP_DF_VIEW = 'd-m-Y';
    const PHP_DF_SQL = 'Y-m-d';
    const PHP_DF_YM = 'Y-m';
    const PHP_DF_DATE = 'Y-m-d';
    const PHP_DF_DATETIME = 'Y-m-d H:i:s';
    /**
     * data format for smarty
     */
    const SMARTY_DF_DATE = '%Y-%m-%d';
    const SMARTY_DF_DATETIME = '%Y-%m-%d %H:%M:%S';
    const SMARTY_DF_HOUR = '%H';
    const SMARTY_DF_MINUTE = '%M';
    const SMARTY_DF_VIEW = '%d-%m-%Y';
    const SMARTY_DF_ADMIN_TABLE_VIEW = '%d/%m/%Y';
    const SMARTY_DF_FRONT_TABLE_VIEW = '%d/%m/%Y';
    const SMARTY_DF_PROFILE = '%d %b %Y';
    /**
     * SEO urls delimiter
     */
    const SEO_DELIMITER = '/';
    /**
     * frontend breadcrumb delimiter
     */
    const FRONTEND_BREADCRUMB_DELIMITER = ' &gt; ';
    /**
     * for admin settings
     */ 
    const SETTINGS_ON_LINE = 5;
    
    /**
     * content id
     */
    const CONTENT_ID = 'cid';
    /**
     * cache value time to live
     */
    const CACHE_TTL = '3600';

    /**
     * array with site parts except administration part in format 'url(after host)'=>'dir_name'
     * By default set administration site part and frontend
     * @var array
     */
    public static $site_parts = array(
    );
    /**
     * array with site parts for administration part in format 'url(after host)'=>'dir_name'
     * In proccess add to keys(url) administration url from config file
     * @var array
     */
    public static $admin_site_parts = array(
    );
    
    /**
     * calculate static variables
     */
    public static function selfCalculate()
    {
        parent::selfCalculate();
        self::$cookie_alive_time = self::$seconds_in_day * 7; // 7 days
        self::$session_alive_time = self::SECONDS_IN_HOUR - (60*20); // 40 mins
        
        foreach(self::$site_parts as &$sp)
        {
            $sp = str_replace('/', DS, $sp);
        }
    }
}

/**
 * call this method to calculate class variables. DO NOT REMOVE
 */
Defines::selfCalculate();

/* End of file Defines.php */
/* Location: ./class/Common/Defines.php */
?>