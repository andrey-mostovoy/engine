<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

Loader::loadClass('Base_Container');
/**
 * class PermissionsContainer
 * Example 1. Create section "test" with roles "user", "admin" and some permissions
 * <code>
 * $container = PermissionsContainer::getInstance()
 * $container->section('test')
 *      ->role('user')
 *      ->add(array(
 *          'action1' => array(
 *              'target1' => true, 
 *              'target2 => false
 *          ),
 *          'action2' => false
 *      )
 *      ->parent()
 *      ->role('admin')
 *      ->add(array('action1' => true));
 * </code>
 * 
 * Example 2. Get permissions for section "test" and role "user"
 * <code>
 * $container->section('test')->role('user')->get();
 * </code>
 * 
 * @package		Base
 * @subpackage	Extension
 * @uses        class/Container
 */
class PermissionsContainer extends Container
{
    const SECTION_PERSONAL    = 'personal';
    const SECTION_COLLECTION  = 'collection';

    protected static $instance;
    
    private function __construct() {}
    
    /**
     * Returns instance
     * 
     * @return object
     */
    public static function getInstance($section = null)
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
        return $this->rewind()->setCurrent($name);
    }
    
    /**
     * Moves to subsection of current section
     * 
     * @param string $name
     * @return object $this 
     */
    public function subSection($name)
    {
        return $this->setCurrent($name);
    }
    
    /**
     * Moves to "personal" section
     * 
     * @param int $id  profile id
     * @return object $this 
     */
    public function personal($id = null)
    {
        return $this->setCurrent(self::SECTION_PERSONAL)->id($id);
    }
    
    /**
     * Moves to "collection" section
     * 
     * @param int $id  id of element in collection
     * @return object $this 
     */
    public function collection($id = null)
    {
        return $this->setCurrent(self::SECTION_COLLECTION)->id($id);
    }
    
    /**
     * Moves to specific "id" in collection
     * 
     * @param int $id
     * @return object $this
     */
    public function id($id)
    {
        return $this->setCurrent($id);
    }
    
    /**
     * Moves to role
     * 
     * @param mixed $name  role identifier
     * @return object $this
     */
    public function role($name)
    {
        return $this->setCurrent($name);
    }
    
    /**
     * Moves to action
     * 
     * @param string $name
     * @return object $this
     */
    public function action($name)
    {
        return $this->setCurrent($name);
    }
    
    /**
     * Moves to target
     * 
     * @param string $name
     * @return object $this
     */
    public function target($name)
    {
        return $this->setCurrent($name);
    }
}