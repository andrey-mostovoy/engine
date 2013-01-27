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

Loader::loadClass('Base_File');

/**
 * class Log
 * class provides static methods to write log file
 *
 * @package		Base
 * @author		amostovoy
 */
class Log
{
    /**
     * directory name for logs from project root
     */
    const DIR = 'log';
    /**
     * log filename
     */
    const FILE = 'error';
    /**
     * extensions for log files
     */
    const EXT = 'log';
    /**
     * mode with file will be write
     */
    const MODE = 'at';
    /**
     * base project url
     * @var string
     */
    public static $dir = '';

    /**
     * Concatenate and return full path to file
     * @param string $filename [optional]
     * @return string
     */
    private static function formatFilename($filename='')
    {
        return self::$dir . self::DIR . DIRECTORY_SEPARATOR . (empty($filename) ? self::FILE : $filename) .'.'.self::EXT;
    }

    /**
     * Add to content some additional info
     * @param string $content
     * @return string
     */
    private static function formatContent($content)
    {
        return "\n" . date('Y-m-d H:i:s') . ':' . "\n" . $content . "\n";
    }

    /**
     * Add new log information to current log file.
     * On debug mode file not write.
     * @param string $text
     * @param string $filename (optional d:'') file to write in. must be in log directory
     * @return bool return true if file was write and false if not
     */
    public static function add($text, $filename='')
    {
        if(is_writable( self::formatFilename($filename) ))
            return Defines::DEBUG ?
                true : File::writeFile(
                        self::formatFilename($filename),
                        self::formatContent($text),
                        self::MODE);
        elseif(Defines::DEBUG)
        {
            echo "\r\n" . 'File '.self::formatFilename().' hasn\'t write permission<pre>';
            var_dump( self::formatContent($text) );
            echo '</pre>';
            return true;
        }
    }

    /**
     * Add new log information to special log file.
     * @param string $filename file must be in log directory
     * @param string $text
     * @return bool return true if file was write and false if not
     */
    public static function addToFile($filename, $text)
    {
        return self::add($text, $filename);
    }
}

/* End of file Log.php */
/* Location: ./class/Base/Log.php */
?>