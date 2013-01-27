<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.5
 * @since		Version 1.3
 * @filesource
 */

/**
 * class Config
 * containing configuration constants for project
 * @package		Base
 * @author		amostovoy
 */
class Config
{
    /**
     * indicate debug mode.
     */
    const DEBUG = 1;

    /**
     * indicate PayPal mode.
     */
    const SANDBOX_MODE = 1;

    /**
     * general settings
     */
    const SITE_NAME = 'jobcentrepod';
    
    /**
     * Database settings
     */
    const DATABASE_SERVER   = 'qs-dev.com';
    const DATABASE_USERNAME = 'jobcentrepod';
    const DATABASE_PASSWORD = 'OCESetkL';
    const DATABASE_DBNAME   = 'jobcentrepod';
    const DATABASE_DBDRIVER = 'mysqli';
    
    /**
     * Cache settings
     * to stop using cache set CACHE = false
     */
    const CACHE         = false;//'Memcache';
    const CACHE_HOST    = 'localhost';
    const CACHE_PORT    = '11211';

    /**
     * Compression settings.
     * If true - will try to compress js and css resources.
     * Better turn off while develope any resource code
     */
    const RESOURCE_COMPRESSION = false;
    
    /**
     * administration settings
     */
    const ADMIN_URL         = 'admin';
    
    /**
     * view settings
     */
    const TEMPLATE_DIR  = 'default';
    
    /**
     * paging settings
     */
    static $numpage_choose = array(1, 2, 10, 25, 50, 100, 200, 500);
    const PAGING_RANGE_NUM                  = 3;
    const NUMPAGE_MAIN                      = 25;
    
    /**
     * php ini directives to set.
     * If hosting not allowed this operation this setting
     * will not be set
     * @var array
     */
    static $php_ini = array(
        'upload_max_filesize' => '1024M',
        'post_max_size' => '1024M',
        'max_execution_time' => -1,
        'memory_limit' => -1
    );
}
/* End of file Configs.php */
/* Location: ./configs/Configs.php */
?>