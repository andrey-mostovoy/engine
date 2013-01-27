<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.26
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base.Controller.ControllerView');

/**
 * class ControllerPermission
 * 
 * main class for all controllers
 * containing basic methods and properties
 * for permissions
 * 
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class ControllerPermission extends ControllerView
{
    /**
     * If need specified which of users is current logged in
     * controller must implement this method
     */
    protected function setCurrentActiveUser() {}
    
    private function checkAccessRules()
    {
        $rules = $this->accessRules();
        
        show($rules);
        
        die();
    }
    /**
	 * Specifies the access control rules.
     * @example
     * return array(
	 *		array('allow',  // allow all users to perform 'index' and 'view' actions
	 *			'actions'=>array('index','view'),
	 *			'users'=>array('*'),
	 *		),
	 *		array('allow', // allow authenticated user to perform 'create' and 'update' actions
	 *			'actions'=>array('create','update'),
	 *			'users'=>array('@'),
	 *		),
	 *		array('allow', // allow admin user to perform 'admin' and 'delete' actions
	 *			'actions'=>array('admin','delete'),
	 *			'users'=>array(User::ADMIN),
	 *		),
	 *		array('deny',  // deny all users
	 *			'users'=>array('*'),
	 *		),
	 *		array('deny',  // deny guest
	 *			'users'=>array('?'),// or self::GUEST
	 *		),
	 *	);
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'manage' actions
				'actions'=>array('manage'),
				'users'=>array('@'),
			),
		);
	}
    /**
     * Check access permission to do something
     * 
     * @param string $section section name
     * @param type $target target of permission
     * @param type $action action on target
     * @param type $redirect (d:true) flag to redirect on failure
     * @return bool
     */
    public function checkAccessPermission($section, $target, $action, $redirect=true)
    {
        if( !App::perm($section)->check($target, $action) )
        {
            if($redirect)
            {
                if(isset($_SERVER['HTTP_REFERER']))
                    $_SESSION['redirect']['back'] = $_SERVER['HTTP_REFERER'];
                
                $this->errorAccess();
            }
            else
                return false;
        }
        return true;
    }
}
/* End of file ControllerPermission.php */
/* Location: ./class/Base/Controller/ControllerPermission.php */
?>