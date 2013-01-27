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
    'user' => array(
        'site_title_admin' => "Users",
        'header' => "Users",
        'breadcrumb_create' => 'User',
        'delete_selected' => "delete_selected",
        'selected' => "selected",
        'role' => "Role",
        'id' => "ID",
        'email' => "Email",
        'password' => "Password",
        'confirm_password' => "Confirm password",
        'first_name' => "First name",
        'last_name' => "Last Name",
    //        ;other
        'status' => "Status",
        'name' => "Name",
        'reg_date' => "Registered date",
        'pers_info' => 'Personal Information',
        'cont_info' => 'Contact Information',
        'birth_date' => 'Date of Birth',
        'sex' => 'Sex',
        'marital' => 'Marital status',
        'children' => 'Number of children',
        'phone' => 'Phone',
        'mobile' => 'Cell Phone',
    ),
    'role' => array(
        'admin' => 'Admin',
        'admin_front' => 'Admin Front',
        'member' => 'User',
    ),
    'status' => array(
        'active' => 'Active',
        'blocked' => 'Blocked',
        'cancel' => 'Cancel',
        'incomplete' => 'Registration incomplete',
    ),
    'sex' => array(
        'male' => 'Male',
        'female' => 'Female',
    ),
    'language_knowledge' => array(
        'beginner' => 'Beginner',
        'elementary' => 'Elementary',
        'intermediate' => 'Intermediate',
        'upper_intermediate' => 'Upper Intermediate',
        'advanced' => 'Advanced',
        'proficient' => 'Proficient',
    ),
);

/* End of file user.php */
/* Location: ./lang/en/user.php */
?>