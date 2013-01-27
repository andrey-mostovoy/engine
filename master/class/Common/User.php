<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Base_User');

/**
 * class User
 * containing methods and class properties for current project only
 *
 * @package		Base
 * @subpackage	Common
 * @category	User
 */
class User extends BaseUser
{
    /**
     * Test user for admin to see how site looks on frontend 
     */
    const ADMIN_FRONT = 8;
    /**
     * User project statuses
     * @var array
     */
    public static $status = array(
        1 => 'active',
        2 => 'blocked',
        3 => 'cancel',
        4 => 'incomplete',
    );
}

/* End of file User.php */
/* Location: ./class/Common/User.php */
?>