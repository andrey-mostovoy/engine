<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage  Lang
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1
 * @since		Version 1.6
 * @filesource
 */

/**
 * English
 * 
 * containing language specific text in array.
 *
 * @package		Base
 * @subpackage  Lang
 * @author		amostovoy
 */
return array(
'file_upload' => array(
    Lang::ERROR => array(
        'err' => 'Unknown upload error',
        'err_1' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        'err_2' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        'err_3' => 'The uploaded file was only partially uploaded',
        'err_4' => 'No file was uploaded',
        'err_6' => 'Missing a temporary folder',
        'err_7' => 'Failed to write file to disk',
        'err_8' => 'File upload stopped by extension',
    )
),
);

/* End of file common.php */
/* Location: ./lang/en/common.php */
?>