<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

Loader::loadClass('Base_Container');
/**
 * class Permission
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @uses class/Container
 */
class Permission extends Container
{
    const PATH_CLUE = '/';
    /**
     * permissions actions
     */
    const ACTION_VIEW   = 'view';
    const ACTION_ADD    = 'add';
    const ACTION_EDIT   = 'edit';
    const ACTION_REMOVE = 'remove';
    const ACTION_SEND   = 'send';
    
    /**
     * permissions target
     */
    const TARGET_PROFILE  = 'profile';
    const TARGET_CONTENT  = 'content';
    const TARGET_DOWNLOAD = 'download';

    /**
     * instance
     * @var object
     */
    private static $instance = null;
    
    /**
     * permission entity for site section
     * @var string
     */
    private $entity = null;
    /**
     * Status for permissions
     * @var mixed
     */
    private $status = null;
    
    /**
     * class construct
     */
    private function __construct(){}
    
    /**
     * return instance by entity
     * @param string $entity
     * @return object
     */
    public static function getInstance($section=null)
    {
        self::$instance === null and self::$instance = new self();
        return self::$instance->section($section);
    }
    
    /**
     * Moves pointer to current section
     * Creates new section if not exist.
     * 
     * @param string $name
     * @return object $this 
     */
    public function section($name)
    {
        $this->entity = $name;
        return $this->rewind()->walk($name);
    }
    
    /**
     * Sets current subsection
     * Uses as start point for Settings::option()
     * 
     * @param string $name  or path like 'section/subsection'
     * @return Settings 
     */
    public function subSection($name)
    {
        return $this->walk($name, true); 
    }
    
    /**
     * Set current entity
     * @deprecated use section()
     */
    public function setEntity($entity)
    {
        return $this->section($entity);
    }
    
    /**
     * Set status for current permissions
     * @param mixed $status 
     */
    public final function setStatus($status)
    {
        if (!empty($this->entity))
        {
            $this->status[$this->entity] = $status;
        }
        return $this;
    }
    /**
     * Return status for current permissions. If fo current entity not set status use global status
     * @param $s (d:null) required status
     * @return mixed
     */
    public final function getStatus($s=null)
    {
        if(empty($s))
            $s = $this->entity;
        return isset($this->status[$s]) ? $this->status[$s] : $this->status['global'];
    }
    /**
     * Append new permissions to exist
     * @param array $p 
     * @deprecated use add()
     */
    public final function append($p)
    {
        return $this->add($p);
    }
    
    /**
     * Check permission 
     * @param string $action
     * @param string $target (d:null)
     * @return bool
     */
    public final function check($target, $action=null)
    {
        $permissions = $this->walk($target)->get();
        
        if(empty($action))
        {
            return !empty($permissions);
        }
        else
        {
            if (is_array($permissions))
            {
                return !empty($permissions[$action]);
            }
            else
            {
                return (bool)$permissions;
            }
        }
    }
}

/* End of file Permission.php */
/* Location: ./class/Permission.php */
?>