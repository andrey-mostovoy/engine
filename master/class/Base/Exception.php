<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * interfaces and classes of project exceptions
 *
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.2
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

    Loader::loadClass('Base_Error');

	interface IException {}
		interface IInternalException extends IException {}
			interface IFileException extends IInternalException {}
			interface IHttpException extends IInternalException {}
			interface IRequestException extends IInternalException {}
			interface IErrorException extends IInternalException {}
		interface IUserException extends IException {}


	class FileNotFoundException extends Error implements IFileException {}
	class FileWrightException extends Error implements IFileException {}
	class HttpException extends Error implements IHttpException {}
	class RequestException extends Error implements IRequestException {}
	class InternalException extends Error implements IInternalException, IUserException {}
	class InternalErrorException extends ErrorException implements IErrorException {}
	class SqlException extends Error implements IInternalException, IUserException {}


	/**
	 * catch all errors as exception
	 */
	function exception_error_handler($errno, $errstr, $errfile, $errline )
	{
		throw new InternalErrorException($errstr, 0, $errno, $errfile, $errline);
		return true;
	}
	if (Defines::HARD_DEBUG)
		set_error_handler("exception_error_handler");


/* End of file Exception.php */
/* Location: ./class/Base/Exception.php */
?>