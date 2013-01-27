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
    'auth' => array(
        'site_title' => 'Authorization',
        'site_title_admin' => 'Administrator authorization',
        'header' => 'Login',
        Lang::ERROR => array(
            'login_incorrect' => 'Email / password incorrect',
            'login_status_cancel_confirm' => 'Your account was cancelled on %date%. Are you sure you want to retrieve the account? (Y / N)',
            'login_status_cancel_message' => 'Your account was cancelled.',
            'restore_canceled_fail' => 'Restore proccess fail.',
            'forgot_incorrect' => 'Email is not registered',
            'forgot_save_error' => 'Can\'t save new assword',
            'forgot_email_send_error' => 'Can\'t send new password',
            'login_status_incomplete' => 'Your account is not complited',
            'login_status_blocked' => 'Your account is blocked',
        ),
    ),
);

/* End of file auth.php */
/* Location: ./lang/en/auth.php */
?>