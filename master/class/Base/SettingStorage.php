<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

Loader::loadClass('Base_Container');
Loader::loadClass('Base_SettingSupplier');

/**
 * Class SettingStorage
 * Stores settings
 * 
 * Example 1. Base usage
 * <code>
 * $settings = Setting::getInstance();
 * or
 * $settings = App::setting();
 * 
 * $settings->section('general')->set('items_on_page', 23);
 * or
 * App::setting('general')->set('items_on_page', 23);
 * or
 * App::setting()->general()->set('items_on_page', 23);
 * or
 * App::setting('general')->items_on_page = 23;
 * also can do chain requests
 * App::setting('general')->set('items_on_page', 23)->set('numvar', 5);
 * 
 * echo $settings->option('items_on_page'); //23
 * //or more short
 * echo $settings->items_on_page; //23
 * 
 * $settings->section('content')->set(array('limit' => 50, 'max_words' => 10));
 * 
 * //note: last mentioned section used as start point for options
 * echo $settings->max_words; //10
 * echo $settings->section('general')->items_on_page; //23
 * echo $settings->max_words; //"null" because section has been changed
 *                            //correct: $settings->section('content')->max_words
 * </code>
 * 
 * Example 2. Suppliers usage
 * Uses previous example. Supplier must implement method settingLoad()
 * <code>
 * echo $settings->section('new')->max_size; //null
 * $settings->setSuppliers(array('new' => 'frontend/modelName'));
 * 
 * echo $settings->max_size; //123
 * //For section 'new' supplier has been called. 
 * //NOTE: supplier responsible for all actions related with "get" settings from db or constants
 * //and "add" them to settings container.
 * </code>
 * @package		Base
 * @subpackage	Extension
 * @uses class/Base/App
 * @uses class/Container
 */
class SettingStorage
{
    const PATH_CLUE = '/';
    
    /**
     * Suppliers container
     * 
     * @var array 
     */
    protected $_supplier = array();
    
    /**
     * Current path
     * 
     * @var string 
     */
    protected $_path = '';
    
    /**
     * Path to current section
     * 
     * @var string 
     */
    protected $_sectionPath = '';
    
    /**
     * Contain settings
     * 
     * @var Container 
     */
    protected $_container;
    
    /**
     * Specific params for suppliers
     * 
     * @var array 
     */
    protected $_params = array();
    
    /**
     * Request for specifc supplier
     * 
     * @var array 
     */
    protected $_request = array();
    
    /**
     * Wrapper to SettingStorage::option()
     * 
     * @param string $name
     * @return mixed 
     */
    public function __get($name)
    { 
        return $this->option($name);
    }
    
    /**
     * Wrapper to SettingStorage::set()
     * 
     * @param string $name
     * @param mixed $value
     * @return SettingStorage
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }
    /**
     * Wrapper to SettingStorage::section()
     * @param string $name
     * @param mixed $arguments
     * @return SettingStorage 
     */
    public function __call($name, $arguments)
    {
        return $this->section($name);
    }
    /**
     * Moves container's pointer to the start position
     * 
     * @return SettingStorage 
     */
    public function rewind()
    {
        $this->_container->rewind();
        return $this->clearPath()->clearSectionPath();
    }
    
    /**
     * Sets or update settings for current option's section
     * 
     * @param string|array $name  for array use key=>value structure
     * @param mixed $value  ignored if $name is array
     * @return SettingStorage 
     */
    public function set($name, $value = null)
    {
        $settings = is_array($name) ? $name : array($name => $value);
        
        $this->_container->add($settings);
        return $this;
    }
    
    /**
     * Wrapper to set()
     * 
     * @param string|array $name
     * @param mixed $value
     * @return array 
     */
    public function add($name, $value = null)
    {
        return $this->set($name, $value);
    }
    
    /**
     * Sets current section
     * Uses as start point for Settings::option()
     * 
     * @param string $name  or path like 'section/subsection'
     * @return SettingStorage 
     */
    public function section($name)
    { 
        return $this->rewind()->walk($name, true); 
    }
    
    /**
     * Sets current subsection
     * Uses as start point for Settings::option()
     * 
     * @param string $name  or path like 'section/subsection'
     * @return SettingStorage 
     */
    public function subSection($name)
    {
        return $this->walk($name, true); 
    }
    
    /**
     * Returns option's value
     * 
     * @param string $name  or path like 'section/optionName'
     * @param mixed  $params  extra data for supplier
     * @return mixed 
     */
    public function option($name=null)
    {  
        $result = $this->walk($name)->get();
        
        if (null === $result)
        {
            $result = $this->loadSupplier()->walk($name)->get();
        }
        
        $this->section($this->getSectionPath());
        return $result;
    }
    
    /**
     * Walks through container 
     * 
     * @param string $path  format: 'path1/path2/path3'
     * @param bool $isSection  save current path as main
     * @return SettingStorage 
     */
    public function walk($path, $isSection = false)
    {
        if (empty($path)) return $this;
        
        if ($isSection)
        { 
            $this->addSectionPath($path)->clearPath()
                ->addPath($this->getSectionPath());
        }
        else
        {
            $this->addPath($path);
        }
        
        foreach ($this->explodePath($path) as $section)
        { 
            $this->_container->setCurrent($section);
        }
        return $this;
    }
    
    /**
     * Adds suppliers' params
     * 
     * @param array $params
     * @return SettingStorage
     */
    public function params($params)
    {
        return $this->appendParams($params);
    }
    
    /**
     * Gets current value from container
     * 
     * @return mixed 
     */
    public function get()
    {
        return $this->_container->get();
    }
    
    /**
     * Adds supplier to list
     * @example
     *  App::setting()->appendSuppliers(array(
     *      'gallery/tag/photo_gallery' => 'frontend/gallery/Photo',
     *      'gallery/tag/phototip' => 'frontend/gallery/Phototips',
     *      'gallery/tag/video_gallery' => 'frontend/gallery/Video',
     *      'gallery/tag/videotip' => 'frontend/gallery/Videotips',
     *  ));
     * @param string $supplier  format: array('section/subsection' => 'path/to/model')
     * @return SettingStorage
     */
    public function appendSuppliers($supplier)
    {
        return $this->setSuppliers(array_merge($this->getSuppliers(), $supplier));
    }
    
    /**
     * Sets supliers list
     * @example
     *  App::setting()->setSuppliers(array(
     *      'gallery/tag/photo_gallery' => 'frontend/gallery/Photo',
     *      'gallery/tag/phototip' => 'frontend/gallery/Phototips',
     *      'gallery/tag/video_gallery' => 'frontend/gallery/Video',
     *      'gallery/tag/videotip' => 'frontend/gallery/Videotips',
     *  ));
     * @param array $supplier
     * @return SettingStorage 
     */
    public function setSuppliers($supplier)
    {
        $this->_supplier = $supplier;
        return $this;
    }
    
    /**
     * Gets suppliers list
     * 
     * @return array 
     */
    public function getSuppliers()
    {
        return $this->_supplier;
    }
    
    /**
     * Loads supplier related with current "path"
     * 
     * @return SettingStorage 
     */
    public function loadSupplier()
    {
        if (($supplier = $this->clearRequest()->getSupplier($this->getPath())))
        { 
            $sectionPath = $this->getSectionPath();
            App::model($supplier, false)->settingLoad(); 
            $this->section($sectionPath); 
        }
        return $this;
    }
    
    /**
     * Finds supplier related with path
     * Move up on path's tree.
     * 
     * @param string $path
     * @return string|null  supplier path 
     */
    public function getSupplier($path)
    {
        if (empty($path))
        {
            return null;
        }
        
        if (!empty($this->_supplier[$path]))
        {
            return $this->_supplier[$path];
        }
        else
        {
            $path = $this->explodePath($path);
            $param = array_pop($path); 
            $this->appendRequest($param);
            $path = $this->implodePath($path); 
            return $this->getSupplier($path);
        }
    }
    
    /**
     * Sets params to supplier's request
     * 
     * @param array $params
     * @return SettingStorage
     */
    public function setRequest($params)
    {
        $this->_request = $params;
        return $this;
    }
    
    /**
     * Appends params to request
     * 
     * @param mixed $supplier
     * @return SettingStorage
     */
    public function appendRequest($request)
    {
        $this->_request[] = $request;
        return $this;
    }
    
    /**
     * Gets current request
     * 
     * @return array
     */
    public function getRequest()
    {
        return array_reverse($this->_request);
    }
    
    /**
     * Clears request
     * 
     * @return SettingStorage
     */
    public function clearRequest()
    {
        $this->_request = array();
        return $this;
    }
    
    /**
     * Adds section to path
     * 
     * @param string $name
     * @return SettingStorage 
     */
    public function addSectionPath($name)
    {
        $clue = !empty($this->_sectionPath) ? self::PATH_CLUE : '';
  
        $this->_sectionPath .= $clue . $name;
        
        return $this;
    }
    
    /**
     * Gets current path
     * 
     * @return string 
     */
    public function getSectionPath()
    {
        return $this->_sectionPath;
    }
    
    /**
     * Adds section to path
     * 
     * @param string $name
     * @return SettingStorage 
     */
    public function addPath($name)
    {
        $clue = !empty($this->_path) ? self::PATH_CLUE : '';
  
        $this->_path .= $clue . $name;
        
        return $this;
    }
    
    /**
     * Gets current path
     * 
     * @return string 
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Explodes path
     * 
     * @param string $path  used current path if null
     * @return array 
     */
    public function explodePath($path = null)
    {
        if (null === $path)
        {
            $path = $this->getPath();
        }

        return explode(self::PATH_CLUE, $path);
    }
    
    /**
     * Implodes path
     * 
     * @param array $path
     * @return string 
     */
    public function implodePath($path)
    {
        return implode(self::PATH_CLUE, (array)$path);
    }
    
    /**
     * Clears path
     * 
     * @return SettingStorage 
     */
    public function clearPath()
    {
        $this->_path = '';
        return $this;
    }
    
    /**
     * Clears section path
     * 
     * @return SettingStorage 
     */
    public function clearSectionPath()
    {
        $this->_sectionPath = '';
        return $this;
    }
    
    /**
     * Adds specific for supplier params
     * 
     * @param array $params
     * @return SettingStorage
     */
    public function appendParams($params)
    {
        return $this->setParams(array_merge($this->getParams(), (array)$params));
    }
    
    /**
     * Gets params
     */
    public function getParams($name = null)
    {
        if (!empty($name) && isset($this->_params[$name]))
        {
            return $this->_params[$name];
        }
        
        return $this->_params;
    }
    
    /**
     * Sets params
     * 
     * @param mixed $params
     * @return SettingStorage
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }
}
/* End of file SettingStorage.php */
/* Location: ./class/Base/SettinStorage.php */
?>