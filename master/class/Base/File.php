<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

/**
 * class File
 * containing methods for work with file system
 * @package		Base
 * @category	File
 * @author		amostovoy
 */
class File
{
    /**
     * Correct modes
     * @var array
     */
    private static $_allowed_mode = array(
              'r',
              'w',
              'a',
              'x'
    );
    /**
     * Correct flags
     * @var array
     */
    private static $_allowed_flag = array(
              't',
              'b'
    );

    /**
     * listing mode constants
     */
    const LISTING_FILES = 0;
    const LISTING_FOLDERS = 1;
    const LISTING_ALL = 2;

    /**
     * Folder listing.
     * @param string $path  path to folder to listing
     * @param int $mode     variants of return results.
     * @return array    depend on $mode return files if $mode 0, folders if $mode 1 and all on $mode 2
     */
    public static function listing($path, $mode=self::LISTING_ALL)
    {
        $folders = $files = array();
        if (is_dir($path))
        {
            if (($dir = opendir($path)))
            {
                while (false !== ($file = readdir($dir)))
                {
                    if ($file != "." && $file != "..")
                    {
                        if(is_dir($path."/".$file))
                        {
                            $folders[] = $file;
                        }
                        else
                        {
                            $files[] = $file;
                        }
                    }
                }//while
            }// second if
            closedir($dir);
        }

        if($mode == self::LISTING_FOLDERS) {return $folders;}
        if($mode == self::LISTING_FILES) {return $files;}
        if($mode == self::LISTING_ALL) {return array_merge($folders, $files);}
    }

    /**
     * Check file or directory for exist
     * @param string $file file or dir path to check
     * @param bool   $is_throw_exception (optional d:false) indicate that failure check throw exception
     * @return bool return true if file or dir exist and false if not
     */
	public static function checkExist($path, $is_throw_exception = false)
	{
		if (!file_exists($path))
        {
            if ($is_throw_exception)
                throw new FileNotFoundException('Can\'t find file <strong>'.$path.'</strong>');
            return false;
        }
        return true;
	}

    /**
     * Wright file. On errors generatate user level warnings
     * @param string $filename  path to the file
     * @param string $content   content to save in file
     * @param string $mode      open file mode
     * @return bool return true if file was write and false if not
     */
    public static function writeFile($filename, $content, $mode='w')
    {
        if (self::checkMode($mode))
        {
            if (!$handle = fopen($filename, $mode))
            {
                trigger_error('Can\'t open file '.$filename );
                return false;
            }
            if (fwrite($handle, $content) === FALSE)
            {
                trigger_error('Can\'t write to the file '.$filename );
                return false;
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    /**
     * Check for correct mode string
     * @param string $mode string with mode to check
     * @return bool return true on correct mode and false on failure
     */
    private static function checkMode($mode)
    {
        if( !empty($mode) && !preg_match('/^('.implode('|', self::$_allowed_mode).')\+{0,1}('.implode('|', self::$_allowed_flag).'){0,1}$/', $mode))
        {
            trigger_error('Wrong mode \''.$mode.'\'' );
            return false;
        }
        return true;
    }

    /**
     * Read file into string
     * @param string $filename path to file
     * @return mixed return string with file contents or false on failure
     */
    public static function readFile($filename)
    {
        if ( !empty($filename) )
        {
            return file_get_contents($filename);
        }
        trigger_error('file name is empty!' );
        return false;
    }

    /**
     * Delete file.
     * @param string $filename path to the file
     * @return bool return true on success or false on failure
     */
    public static function deleteFile($filename)
    {
        return unlink($filename);
    }

    /**
     * Copy file. If the destination file already exists, it will be overwritten.
     * @param string $source path to source file
     * @param string $dest <p>
     * The destination path.
     * If dest is a URL, the copy operation may fail if the wrapper does not support overwriting of existing files.
     * If the destination file already exists, it will be overwritten.
     * </p>
     * @return bool return true on success or false on failure
     */
    public static function copyFile($source, $dest)
    {
        if ( !empty($source) && !empty($dest) )
        {
            return copy($source, $dest);
        }
        trigger_error('source or dest file name is empty!' );
        return false;
    }

    /**
     * Move file. If the destination file already exists, it will be overwritten.
     * @param string $source path to source file
     * @param string $dest <p>
     * The destination path.
     * If the destination file already exists, it will be overwritten.
     * </p>
     * @return bool return true on success or false on failure
     */
    public static function moveFile($source, $dest)
    {
        if ( !empty($source) && !empty($dest) )
        {
            return self::rename($source, $dest);
        }
        trigger_error('source or dest file name is empty!' );
        return false;
    }

    /**
     * Rename file. Can also move file
     * @param string $oldname path to old file or dir name and it position
     * @param string $newname path to new file or dir name and it position
     * @return bool return true on success or false on failure
     */
    public static function rename($oldname, $newname)
    {
        if ( !empty($oldname) && !empty($newname) )
        {
            return rename($oldname, $newname);
        }
        trigger_error('old name or new name file/dir name is empty!' );
        return false;
    }

    /**
     * Create directory. If directory already exist function doesn't create dir and return true
     * @param string $pathname The directory path.
     * @param int $chmode (optional d:0777) dir permission
     * @return bool return true on success or false on failure
     */
    public static function createDir($pathname, $chmode=0777)
    {
        if ( !self::checkExist($pathname) )
            return mkdir($pathname, $chmode);
        return true;
    }

    /**
     * Copy directory.
     * @param string $source
     * @param string $target
     */
    public static function copyDir($source, $target)
    {
        if ( is_dir( $source ) )
        {
            self::createDir($target);
            $d = dir( $source );
            while ( FALSE !== ( $current_entry = $d->read() ) )
            {
                if ( $current_entry != '.' && $current_entry != '..' )
                {
                    $entry = $source . DIRECTORY_SEPARATOR . $current_entry;
                    if ( is_dir( $entry ) )
                    {
                        self::copyDir( $entry, $target . DIRECTORY_SEPARATOR . $current_entry );
                        continue;
                    }
                    self::copyFile( $entry, $target . DIRECTORY_SEPARATOR . $current_entry );
                }
            }
            $d->close();
        }else {
            self::copyFile( $source, $target );
        }
    }

    /**
     * @todo
     */
    public static function moveDir()
    {

    }

    /**
     * Remove directory and all inside them.
     * @param string $directory path to directory
     * @param bool $delete_dir set it to true if want to delete init dir
     * @return bool return true on success or false on failure
     */
    public static function deleteDir($directory, $delete_dir = true)
    {
        if(!is_dir($directory))
        {
            return false;
        }
        
        $handle = opendir($directory);
        while(false !== ($file = readdir($handle)))
        {
            if ( is_file($directory.DIRECTORY_SEPARATOR.$file))
            {
                self::deleteFile($directory.DIRECTORY_SEPARATOR.$file);
            }
            elseif ( is_dir($directory.DIRECTORY_SEPARATOR.$file) && ($file != ".") && ($file != "..") )
            {
                self::deleteDir($directory.DIRECTORY_SEPARATOR.$file);
            }
        }
        closedir ($handle);
        if($delete_dir)
        {
            return rmdir($directory);
        }
        else
        {
            return true;
        }
    }
    
    /**
     * Clear directory. remove all inside them but not delete them self
     * @param string $directory path to directory
     * @return bool return true on success or false on failure 
     */
    public static function clearDir($directory)
    {
        return self::deleteDir($directory, false);
    }

    /**
     * Change mode access.
     * @param string $filename  path to file
     * @param int $mode <p>
     * The mode parameter consists of three octal number components specifying access restrictions for the owner,
     * the user group in which the owner is in, and to everybody else in this order.
     * </p>
     * @return bool return true on success or false on failure
     */
    public static function setMod($filename, $mode)
    {
        if (is_int($mode) )
        {
            return chmod($filename, octdec($mode));
        }
        trigger_error('mode must be int!' );
        return false;
    }

    /**
     * Change owner of file.
     * @param string $filename  path to file
     * @param int $user <p>
     * A user name or number.
     * </p>
     * @return bool return true on success or false on failure
     */
    public static function setOwner($filename, $user)
    {
        if ( !empty($filename) && !empty($user) )
        {
            return chown($filename, $user);
        }
        trigger_error('filename or user is empty!' );
        return false;
    }

    /**
     * Change group of file
     * @param string $filename  path to file
     * @param int $mode <p>
     * A group name or number.
     * </p>
     * @return bool return true on success or false on failure
     */
    public static function setGroup($filename, $group)
    {
        if ( !empty($filename) && !empty($group) )
        {
            return chgrp($filename, $group);
        }
        trigger_error('filename or group is empty!' );
        return false;
    }

    /**
     * This function takes a path to a file to output ($file),
     * the filename that the browser will see ($name) and
     * the MIME type of the file ($mime_type, optional).
     * If you want to do something on download abort/finish,
     * register_shutdown_function('function_name');
     * 
     * @param string $file path to the file
     * @param string $name (d:'') name of the file
     * @param string $mime_type (d:'') mime type of the file
     */
    public static function outputFile($file, $name='', $mime_type='')
    {
        self::checkExist($file, true);

        $size = filesize($file);
        if (empty($name))
            $name = basename($file);
        else
            $name = rawurldecode($name);

//        header("Content-type: application/force-download");
//        header('Content-Disposition: inline; filename="' . $file . '"');
//        header("Content-Transfer-Encoding: Binary");
//        header("Content-length: ".$size);
//        header('Content-Type: application/octet-stream');
//        header('Content-Disposition: attachment; filename="' . $name . '"');
//        readfile("$file");
//
//        exit();

        /* Figure out the MIME type (if not specified) */
        $known_mime_types=array(
            "pdf" => "application/pdf",
            "txt" => "text/plain",
            "html" => "text/html",
            "htm" => "text/html",
            "exe" => "application/octet-stream",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg"=> "image/jpg",
            "jpg" =>  "image/jpg",
            "php" => "text/plain",
            'flv' => 'video/x-flv'
        );

        if($mime_type=='')
        {
            $file_extension = strtolower(substr(strrchr($file,"."),1));
            if(array_key_exists($file_extension, $known_mime_types))
            {
                $mime_type=$known_mime_types[$file_extension];
            } else {
                $mime_type="application/force-download";
            };
        };

        @ob_end_clean(); //turn off output buffering to decrease cpu usage

        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

         header('Content-Type: ' . $mime_type);
         header('Content-Disposition: attachment; filename="'.$name.'"');
         header("Content-Transfer-Encoding: binary");
         header('Accept-Ranges: bytes');

         /* The three lines below basically make the
            download non-cacheable */
         header("Cache-control: private");
         header('Pragma: private');
         header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

         // multipart-download and download resuming support
         if(isset($_SERVER['HTTP_RANGE']))
         {
             list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
             list($range) = explode(",",$range,2);
             list($range, $range_end) = explode("-", $range);
             $range=intval($range);
             if(!$range_end) {
                $range_end=$size-1;
             } else {
                $range_end=intval($range_end);
             }

             $new_length = $range_end-$range+1;
             header("HTTP/1.1 206 Partial Content");
             header("Content-Length: $new_length");
             header("Content-Range: bytes $range-$range_end/$size");
         } else {
             $new_length=$size;
             header("Content-Length: ".$size);
         }

        /* output the file itself */
        $chunksize = 1*(1024*1024); //you may want to change this
        $bytes_send = 0;
        if ($file = fopen($file, 'r'))
        {
            if(isset($_SERVER['HTTP_RANGE']))
            fseek($file, $range);

            while(!feof($file) &&
                (!connection_aborted()) &&
                ($bytes_send<$new_length)
                )
            {
                $buffer = fread($file, $chunksize);
                print($buffer); //echo($buffer); // is also possible
                flush();
                $bytes_send += strlen($buffer);
            }
        fclose($file);
        } else die('Error - can not open file.');

        die();
    }
    
    /**
     * Convert path to specific for system
     */
    public static function getPath($path)
    {
        return str_replace('/', DS, $path);
    }
}

/* End of file File.php */
/* Location: ./class/Base/File.php */
?>