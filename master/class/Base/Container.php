<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author      ayakushin, amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

/**
 * class Container
 * 
 * @package		Base
 * @subpackage	Extension
 * @author      ayakushin, amostovoy
 */
class Container
{
    const PATH_CLUE = '/';
    
    /**
     * data list
     * 
     * @var array
     */
    protected $data = array();
    
    /**
     * Pointer to current position
     * 
     * @var &array
     */
    protected $current;
    
    /**
     * Contain pointers to previous tree nodes
     * 
     * @var array
     */
    protected $stack = array();
    
    /**
     * getter 
     * @param string $action data name
     * @return bool|array return data
     */
    public final function __get($name)
    {
        return $this->walk($name)->get();
    }
    
    /**
     * Checks is data name set
     * Needed for correct work of function "empty"
     * 
     * @link http://www.php.net/manual/en/function.empty.php#93117
     * @param string $action
     * @return bool
     */
    public final function __isset($name)
    {
        if(isset($this->current[$name]))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Checks is child element already set
     * 
     * @param string|int $element
     * @return bool
     */
    public final function isExist($element)
    {
        return isset($this->current[$element]);
    }
    
    /**
     * Checks is current element empty
     * 
     * @return bool
     */
    public final function isEmpty()
    {
        return empty($this->current);
    }
    
    /**
     * Moves to parent element
     * 
     * @return object $this 
     */
    public final function parent()
    {
        $index = count($this->stack) - 1;
        if ($index >= 0)
        {
            $this->current = &$this->stack[$index];
            array_pop($this->stack);
        }
    
        return $this;
    }
    
    /**
     * @todo
     */
//    public final function parents($name)
//    {
//        $index = count($this->stack) - 1;
//        if ($index >= 0)
//        {
//            $this->current = &$this->stack[$index];
//            array_pop($this->stack);
//        }
//    
//        return $this;
//    }
//    
//    public final function back()
//    {
//        
//    }
//    
//    public final function next()
//    {
//        
//    }
    
    /**
     * Moves pointer to start position
     * 
     * @return object $this 
     */
    public final function rewind()
    {
        $this->current = &$this->data;
        $this->clearStack();
        return $this;
    }
    
    /**
     * Gets current data
     * 
     * @return array 
     */
    public final function get()
    {
        return $this->current;
    }
    public final function &getByReference()
    {
        return $this->current;
    }
    
    /**
     * Gets all data
     * 
     * @return type 
     */
    public final function getAll()
    {
        return $this->data;
    }
    
    /**
     * Adds data to current element
     * 
     * @param array $permissions
     * @return object $this 
     */
    public final function add($data)
    {
        if (null === $data) 
        {
            return $this;
        }
        elseif (is_string($data))
        {
            $this->current = $data;
        }
        else
        {
            $this->current = $this->mergeArrays((array)$this->current, (array)$data);
        }
        
        return $this;
    }
    
    /**
     * Sets value for current element
     * 
     * @param mixed $value 
     * @return object $this 
     */
    public final function set($value)
    {
        $this->current = $value;
        return $this;
    }
    
    /**
     * Copy some child from parent section
     * 
     * @param string $name
     * @return object $this
     */
    public final function copy($name)
    {
        $temp = &$this->current;
        $copy = $this->parent()->setCurrent($name)->get();
        
        $this->current = &$temp;
        $this->add($copy);
        return $this;
    }
    
    /**
     * Moves pointer to current position
     * 
     * @deprecated
     * @param string|int $name
     * @return object $this
     */
    public function setCurrent($name)
    {
        return $this->child($name);
    }
    
    /**
     * Moves pointer to child position
     * 
     * @param string|int $name
     * @return object $this
     */
    public final function child($name)
    {
        if (!isset($name))
        {
            return $this;
        }
  
        if (!isset($this->current[$name]))
        {
            $this->current[$name] = null;
        }

        $this->stack[] = &$this->current;
        $this->current = &$this->current[$name];
        return $this;
    }
    
    /**
     * Clears container
     * 
     * @return Container
     */
    public final function clear()
    {
        $this->data = array();
        return $this->rewind();
    }
    
    /**
     * Merges two arrays recursively
     * Doesn't create new subarrays (not like array_merge_recursive)
     * 
     * @param array $arr1
     * @param array $arr2
     * @return array
     */ 
    public function mergeArrays($arr1, $arr2)
    {
        foreach($arr2 as $key => $value)
        {  
            if (isset($arr1[$key]) && !is_array($arr1[$key]))
            {
                $arr1[$key] = $value;
            }
            if(array_key_exists($key, (array)$arr1) && is_array($value))
            {
                $arr1[$key] = $this->mergeArrays($arr1[$key], $arr2[$key]);
            }
            else
            {
                $arr1[$key] = $value;
            }  
        }
      
        return $arr1;
    }
    
    /**
     * Clears stack
     * 
     * @return object $this 
     */
    protected final function clearStack()
    {
        $this->stack = array();
        return $this;
    }
    
    /**
     * Walks through container 
     * 
     * @param string $path  format: 'path1/path2/path3'
     * @return Permission
     */
    public final function walk($path)
    {
        if (empty($path)) return $this;
        
        foreach ($this->explodePath($path) as $section)
        {
            $this->child($section);
        }
        return $this;
    }
    
    /**
     * Explodes path
     * 
     * @param string $path  used current path if null
     * @return array 
     */
    private function explodePath($path)
    {
        return explode(self::PATH_CLUE, $path);
    }
}