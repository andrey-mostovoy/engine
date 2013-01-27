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
'general' => array(
    'click_here' => 'Click here',
    'no_data' => 'No Data',
    'home' => 'Home',
    'welcome' => 'Welcome',
    Lang::ERROR => array(
        'empty_field' => 'Please fill mandatory fields',
        'email' => 'Incorrect E-mail',
        'no_content' => 'No content',
    ),
),
'paging' => array(
    'first' => '&laquo;Start',
    'prev' => '&lt;',
    'prev_range' => '...',
    'next' => '&gt;',
    'next_range' => '...',
    'last' => 'Last&raquo;',
    'all' => 'Show All',
    'show_more' => 'Show more',
    'visible_row' => 'Visible row',
),
'breadcrumb' => array(
    'create' => 'Create',
    'settings' => 'Settings',
),
'form' => array(
    'default_select' => 'Please select',
    'select' => 'Select',
    'sort_by' => 'Sort by',
    'required_tip' => 'All Required Fields Are Marked With An Asterick.',
    'default_select_day' => 'Day',
    'default_select_month' => 'Month',
    'default_select_year' => 'Year',
    'title' => 'Title',
    'description' => 'Description',
    'keywords' => 'Keywords',
    'search_results' => 'Search Results',
    Lang::ERROR => array(
        'empty' => '{field} field is required!',
        'wrong' => 'Incorrect',
        'short' => '{field} value too short. Must be more than {length}',
        'long' => '{field} value too long. Must be less than {length}',
        'unique' => '{field} is unique',
        'mismatch' => 'Values not match. {field1} and {field2} must be equal.',
        'string' => '{field} field should be a string',
        'less' => 'Value less',
        'wysiwyg' => '{field} field should contain the content',
        'user_not_exist' => 'No user found',
        'user_not_activated' => 'Account is not activated, please click on the link in your activation email',
        'user_not_active' => 'Account has been blocked. Please check your email for details',
        'maxwordcount' => 'Word count is more than expected',
        'minwordcount' => 'Word count is less than expected',
        'queue_limit' => 'You have attempted to queue too many files.',
        'uniqua' => 'A profile with this email address already exists!',
        'up_incorrect' => 'The username or password, you have entered, is incorrect. Please try again or click Forgot your password',
        'uniq' => 'A profile with this email address already exists. Please Sign In, or Sign Up with a different email.',
        'uniqu' => 'A profile with this email address already exists. Please enter different email.',
        'activate_first' => 'Please activate your account first. Activation letter was sent on registered mail.',
        'email_empty' => 'Please enter email!',
        'password_empty' => 'Please enter password!',
        'email_wrong' => 'Please Check Your Email',
        'real_pass_empty' => 'Please enter password!',
        'no_users_checked' => 'No users was checked.',
    )
),
'button' => array(
    'add' => 'Add',
    'create' => 'Create',
    'new' => 'New',
    'delete' => 'Delete',
    'remove' => 'Remove',
    'hide' => 'Hide',
    'back' => 'Back',
    'login' => 'Login',
    'cancel' => 'Cancel',
    'clear' => 'Clear',
    'reset' => 'Reset',
    'close' => 'Close',
    'save' => 'Save',
    'delete_selected' => 'Delete',
    'edit' => 'Edit',
    'apply' => 'Apply',
    'search' => 'Search',
    'view' => 'View',
    'logout' => 'Logout',
    //other
    'pages' => 'Pages',
    'download' => 'Download',
    'preview' => 'Preview',
    'discard' => 'Discard',
),
'index' => array(
    'site_title_admin' => 'Administrator login',
    'site_title' => 'Twitter Picks, Predictions and Bets. Tweet to pick.',
    'header' => 'Login',
),
'static' => array(
    'site_title_admin' => 'Static Pages',
    'header' => 'Static Pages',
    'breadcrumb_create' => 'Static Page',
    'tab_public' => 'Public',
    'tab_private' => 'Private',
    'tab_guide' => 'Guide',
    'id' => 'ID',
    'parent_id' => 'Parent ID',
    'title' => 'Title',
    'url' => 'Alias URL',
    'meta_keywords' => 'Meta keywords',
    'meta_description' => 'Meta description',
    'content' => 'Content',
    'publish' => 'Publish',
),
);

/* End of file common.php */
/* Location: ./lang/en/common.php */
?>