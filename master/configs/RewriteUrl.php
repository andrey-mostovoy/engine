<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.1
 * @since		Version 1.4
 * @filesource
 */

/**
 * class RewriteUrl
 * containing rewrite urls with asociated modules
 * @package		Base
 * @author		amostovoy
 */
class RewriteUrl
{
    /**
     * array containing rewrite urls for site
     * @example
     * %host%/about will lead to static controller and about action of frontend site part
     * if set to this array following element:
     * 'about' => array('part'=>'frontend','controller'=>'static','action'=>'about')
     * site part frontend set by default so could not set
     * @var array
     */
    protected static $rewrite_urls = array(
    );
}
/* End of file RewriteUrl.php */
/* Location: ./configs/RewriteUrl.php */
?>