<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * 
 * @package		Base
 * @author		ashushunov, amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.31
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

/**
 * class Cache
 * 
 * @package		Base
 * @category	Cache
 * @author		ashushunov, amostovoy
 */
class Cache
{
    const TRIGGER_PREFIX = 'table_trigger_';
    const HASH_TRIGGER_PREFIX = 'hash_';
    
    const TABLE_TRIGGER_TTL = '1800';    // 30 min
    
    private static $allow_classes = array(
        'Memcached', 
		'Memcache'
    );
    private $cache_class;
    
    private static $instance = null;
    
    public $obj = null;

    private function __construct()
    {
        $this->cache_class = Config::CACHE;
        $persistant_id = Config::SITE_NAME.'_pool';
        
        if($this->cache_class)
            if(in_array($this->cache_class, self::$allow_classes))
            {
                if(class_exists($this->cache_class))
                {
                    $this->conn();
                }
                elseif(Config::DEBUG)
                {
                    trigger_error('Can\'t create cache class');
                }
            }
            else
            {
                trigger_error('Wrong cache class');
            }
    }
    
    public static function getInstance()
    {
        if(self::$instance === null)
        {
            self::$instance = new self();
        }
		return self::$instance;
    }
    
    public function conn()
    {
        $cache_class = $this->cache_class;
        $this->obj = new $cache_class();
        return $this->obj->connect(Config::CACHE_HOST, Config::CACHE_PORT);
    }
    
    public function getQueryResult($sql, $tables) {
        if (is_null($this->obj)) {
            return null;
        }
        $this->conn();
        
        $hash = md5($sql);
        
        foreach ($tables as &$value) {
            $value = self::TRIGGER_PREFIX.trim($value,'`');
        }
        unset($value);
        $table_triggers = $this->obj->get($tables);
        if(!empty($table_triggers))
        {
            foreach($table_triggers as $tt=>$v)
            {
                // get hashes for table
                $hashes = $this->obj->get(self::HASH_TRIGGER_PREFIX.$tt);
                if(!empty($hashes))
                {
                    foreach($hashes as $h=>$vv)
                    {
                        //delete hashes
                        $this->obj->delete($h);
                    }
                    //delete table of hashes for table
                    $this->obj->delete(self::HASH_TRIGGER_PREFIX.$tt);
                }
                //delete table triger
                $this->obj->delete($tt);
//                $this->obj->set($tt, 0, false, self::TABLE_TRIGGER_TTL);
            }
            return false;
        }
        else
        {
            // store hash in hash table for table
            foreach($tables as &$table)
            {
                $key = self::HASH_TRIGGER_PREFIX.trim($table,'`');
                $hashes = $this->obj->get($key);
                $hashes[$hash] = 1;
                $this->obj->set($key, $hashes, false, self::TABLE_TRIGGER_TTL);
            }
            unset($table);
        }
        return $this->obj->get($hash);
    }
    
    public function setQueryResult($sql, $data, $ttl) {
        if (is_null($this->obj)) {
            return;
        }
        $this->conn();
        $hash = md5($sql);
        $this->obj->set($hash, $data, false, $ttl);
    }
    
    public function setResetFlag($tables) {
        if (is_null($this->obj)) {
            return;
        }
        $this->conn();
        foreach ($tables as $key) {
            $key = self::TRIGGER_PREFIX.trim($key,'`');
            $this->obj->set($key, 1, false, self::TABLE_TRIGGER_TTL);
        }
    }
}

/* End of file Cache.php */
/* Location: ./class/Base/DB/Cache.php */
?>