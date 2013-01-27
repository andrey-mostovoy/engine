<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base_Log');

/**
 * class Error
 * class provides errors collecting
 *
 * @package		Base
 * @author		amostovoy
 */
class Error extends Exception
{
    /**
     * define an assoc array of error string
     * in reality the only entries we should
     * consider are E_WARNING, E_NOTICE, E_USER_ERROR,
     * E_USER_WARNING and E_USER_NOTICE
     * @var array
     */
    public static $errortype = array (
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
        8192                 => 'Deprecated',
        16384                => 'User Deprecated'
    );
        
    public static function triggerError($error_text, $level=E_USER_WARNING, $file=null, $line=null)
    {
        // Handle user errors, warnings, and notices ourself
        // Show not all errors because smarty generate to many errors
//        if($level != E_NOTICE && $level != 8192)
        if($level != E_NOTICE)
        {
        
            $debug=debug_backtrace();
            //Get the caller of the calling function and details about it
            $callee = $debug[1];
            if(isset($callee['file']))
                $error_text .= ' ||| === >>> in <strong>'
                            .$callee['file'].'</strong> on line <strong>'
                            .$callee['line'].'</strong>';
        
            if (App::request()->isAjax())
            {
                echo Error::$errortype[$level] . ': ';
                print_r($error_text); 
                echo "\n"; echo '--------------------'; echo "\n";
            }
            else
            {
                echo '<div style="background:#fff">';
                    echo '<h3 style="color:red;">'.Error::$errortype[$level].':</h3>';
                    echo '<p>' . $error_text .'</p>';
                echo '</div>';
            }
            
//            if($level != E_NOTICE)
//            {
                Log::add($error_text);
//            }
            return true; //And prevent the PHP error handler from continuing
        }
        return false; //Otherwise, use PHP's error handler
    }
}

//if(!Config::DEBUG)
//{
    function error_handler($level, $message, $file, $line, $context)
    {
        if(0 !== error_reporting())
            return Error::triggerError($message, $level, $file, $line);
        return false;
    }

    //Use our custom handler
    set_error_handler('error_handler');
//}

/* End of file Error.php */
/* Location: ./class/Base/Error.php */
?>