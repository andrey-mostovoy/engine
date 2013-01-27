<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Common_DB_ModelError');

/**	
 * class ModelPermission.
 * Containing common methods and class properties for work with permissions.
 * {@uses Permission}
 * {@uses PermissionsContainer}
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelPermission extends ModelError
{
    const PERM = 'perm'; //for content entry
    const PERM_SECTION = 'perm_section';
    const PERM_IS_SINGLE = 'is_single';
    /**
     * Indicate using permissions, append
     * them to content results
     * @var bool
     */
    public $use_permission = false;
    
    protected function __construct()
    {
        parent::__construct();
        
        if($this->use_permission)
            $this->initPermissions();
    }
    /**
     * Permissions functionality
     */
    protected function initPermissions()
    {
        if (!App::perm_container('global')->isEmpty())
        {
            $this->initContentPermissions();
            return;
        }
        
        App::perm_container('global')
                ->role(User::GUEST)
                    ->add(array(
                        Permission::TARGET_CONTENT => array(
                            Permission::ACTION_VIEW => true,
                        ),
                    ))
            ->parent()
                ->role(User::USER)->copy(User::GUEST)
                    ->add(array(
                        Permission::TARGET_PROFILE => array(
                            Permission::ACTION_VIEW => true
                        ),
                    ))
            ->parent()
                ->role(User::MEMBER)->copy(User::USER)
                    ->add(array(
                        Permission::TARGET_CONTENT => array(
                            Permission::ACTION_ADD => true
                        )
                    ))
            ->parent()
                ->role(User::OWNER)->copy(User::MEMBER)
                    ->add(array(
                        Permission::TARGET_CONTENT => array(
                            Permission::ACTION_EDIT   => true,
                            Permission::ACTION_REMOVE => true,
                        ),
                        Permission::TARGET_PROFILE => array(
                            Permission::ACTION_EDIT => true,
                            Permission::ACTION_REMOVE => true,
                        )
                     ))
            ->parent()
                ->role(User::ADMIN_FRONT)->copy(User::OWNER)
            ->parent()
                ->role(User::ADMIN)->copy(User::ADMIN_FRONT);
        
        App::perm('global')->setStatus(App::user()->getStatus());
        
        $this->initContentPermissions();
        
        App::perm('global')->add(
            App::perm_container('global')->role(App::perm('global')->getStatus())->get()
        );
    }
    
    protected function initContentPermissions()
    {
    }
     
    /**
     * Defines permissions for each entry in content
     * 
     * @param array  $content  multidimensional
     * @param string $section  section name of permissions  
     * @param bool   $is_single  if need permissions for one entry only. 
     *                           Permissions will be added to App::perm()
     */
    public function defineContentPermissions($content, $section, $is_single = false)
    {
        if(!$is_single)
        {
            foreach ($content as $key => &$entry)
            {
                $entry[self::PERM] = $this->runPermissionsMethod($section, $entry);
            }
        }
        else
        {
            if ($is_single && !empty($content[0][self::PERM])) 
            {
                App::perm($section)->append($content[0][self::PERM]);
            }
        }
        return $content;
    }
    
    /**
     * Runs permissions method related with entry
     * 
     * @param string $section
     * @param array  $entry
     * @return array 
     */
    private function runPermissionsMethod($section, $entry)
    {
        $method_name = 'get' . ucfirst($section) . 'Permissions';

        if (method_exists($this, $method_name))
        {
            return $this->$method_name($entry, $section);
        }
        
        return $this->getContentPermissions($entry, $section);
    }
    
    /**
     * Defines permissions for specific entry
     * Default method
     * 
     * @param array $entry
     * @param array $perm (d:null)
     * @return array
     * Result example
     * <code>
     * array(
     *      action1 => array(
     *          target1 => true,
     *          target2 => false,
     *      ),
     *      action2 => true,
     * );
     * </code>
     */
    protected function getContentPermissions($entry, $section)
    {
        return $this->getPermissions($section, $entry);
    }
    
    protected function getPermissionRole($entity, $entry)
    {
        if(isset($entry['is_owner']) && !empty($entry['is_owner']))
        {
            return User::OWNER;
        }
        
        return App::user()->getStatus();
    }
    /**
     * Get permissions. check for current content permission if not set use model permissions
     * @param string $section
     * @param array  $entry
     * @return array 
     */
    protected function getPermissions($section, $entry, $role=null)
    {
        if(is_null($role))
            $role = $this->getPermissionRole($section, $entry);

        $container = App::perm_container($section)->role($role);
        if (!$container->isEmpty())
        {
            return $container->get();
        }
      
        return App::perm_container('global')->role($role)->get(); //change to "content" after add permission there
    }
}

/* End of file ModelPermission.php */
/* Location: ./class/Common/DB/ModelPermission.php */
?>
