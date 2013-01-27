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
'admin' => array(
    'site_title' => 'Administration panel',
    'login' => 'Login',
    'pass' => 'Password',
    'users_export' => 'Users export',
    'welcome_message' => 'Welcome to the CruiseDirector admin section!',
    'success_manage' => 'Operation is successful',
    Lang::ERROR => array(
        'error_login' => 'Incorrect email or password',
        'delete_myself' => 'You try to delete youself!',
    )
),
'admin_table' => array(
    'actions' => 'Actions',
    'are_you_sure' => 'Are you sure?',
    'reset_complete' => 'Reset password complete',
),
);

/* End of file admin.php */
/* Location: ./lang/en/admin.php */
?>