<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.3
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

function __autoload($name)
{
    Loader::loadClass($name);
}

// include core classes
Loader::loadClass(array(
    'Common_Defines',   // load also base Defines and Config
    'Base_Session',
    'Base_App',
    'Base_Setting',
    'Base_File',
    'Base_Log',
    'Base_Error',
    'Base_Exception',
    'Common_User',  // load also base User
    'Base_Request',
    'Common_Controller',    // load also base Controller
//    'Base_Widget',
    'Base_Permission',
    'Base_PermissionsContainer',
    'Common_Validation',    // load also base Validation
    )
);

/**
 * class Manager
 * create User and Request instances
 * controling user session and clone it if exist
 * execute creation requested controller class and call requested method
 *
 * @package		Base
 * @author		amostovoy
 */
class Manager
{
	/**
	 * instance of class
	 * @var Manager
	 * @static
	 */
	private static $_instance = null;

	/**
	 * class construct
	 * create new user if them not save in session
	 * or clone otherwise
	 */
	private function __construct()
	{
        App::session();
        
        App::setting();
        
		//get and create constant for project db driver
		Loader::loadClass('Common_DB_Model');
        
		App::request();
        
		App::user();
	}

	/**
	 * class destruct
	 * save user object in session
	 */
	public function __destruct()
	{
		App::user()->setToSession();
//		App::user()->setToCookie();
	}

	/**
	 * close clone class
	 */
	public function __clone()
	{
		throw new InternalException('Denied to clone object of class '.__CLASS__);
	}

	/**
	 * Return instance of Manager class
	 * @return Manager
	 * @static
	 */
	public static function getInstance()
	{
		self::$_instance === null and self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * function begin process query
	 */
	public final function run()
	{
		$controllerClass = ucfirst(App::request()->getControllerName());
		$actionName = App::request()->getActionName().Request::ACTION_SUFFIX;

		$this->_execute($controllerClass, $actionName);
	}

	/**
	 * execute request query
	 *
	 * create new controller object (fabric pattern),
	 * call needed action and show right template
	 * @param string $controller
	 * @param strting $action
	 * @param mixed $params
	 */
	private function _execute($controller, $action, $params=null)
	{
        $controllerObj = App::controller($controller);

		$inst = Request::CONTROLLER_SUFFIX;
		if (!($controllerObj instanceof $inst))
			throw new RequestException('Controller must be extends class - '.Request::CONTROLLER_SUFFIX, E_USER_ERROR);

		if( !method_exists($controllerObj,$action) && !method_exists($controllerObj,'__call') )
			throw new RequestException('Can\'t find method '.$action);

        $run = true;
        if(method_exists($controllerObj, Request::BEFORE_ACTION_PREFIX.ucfirst($action)))
        {
            $run = $run && call_user_func(array($controllerObj, Request::BEFORE_ACTION_PREFIX.ucfirst($action)), $params);
        }
        
        if($run)
            call_user_func(array($controllerObj, $action), $params);
        
        if(method_exists($controllerObj, Request::AFTER_ACTION_PREFIX.ucfirst($action)))
        {
            call_user_func(array($controllerObj, Request::AFTER_ACTION_PREFIX.ucfirst($action)), $params);
        }

		$controllerObj->_end();
	}

	/**
	 * initial error query
	 * @param Exception $r_exc Exception object by reference
	 */
	public final function error($r_exc)
	{
		/**
		 * process code error
		 * @todo need process it
		 */
//		echo '<pre>';
//		var_dump($exc->getCode());
//		echo '</pre>';
		/**
		 * different actions on different errors
		 * @todo find another way
		 */
// 		if ($r_exc instanceof IErrorException) {}
//		elseif ($r_exc instanceof IRequestException) {}
//		elseif ($r_exc instanceof IHttpException) {}
//		elseif ($r_exc instanceof IFileException) {}
//		elseif ($r_exc instanceof IInternalException) {}
//		elseif ($r_exc instanceof IUserException) {}
//		elseif ($r_exc instanceof IException) {}
//echo '<pre>';
//var_dump($r_exc);
//echo '</pre>';
        Log::add("$r_exc");

		$errorController = ucfirst(App::request()->setControllerName(Request::DEFAULT_ERROR_CONTROLLER));
		$errorAction = App::request()->setActionName(Request::DEFAULT_ERROR_ACTION).Request::ACTION_SUFFIX;

//		App::request()->setSitePart();
		$this->_execute($errorController, $errorAction, $r_exc);
	}
}

/* End of file Manager.php */
/* Location: ./class/Base/Manager.php */
?>