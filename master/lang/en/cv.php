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
    'cv' => array(
        'site_title_admin' => 'CV admin',
        'site_title' => 'CV',
        'header' => 'My C.V. List',
        'breadcrumb_create' => 'new CV on-line',
        'name' => 'CV Name',
        'date' => 'Date',
        'button_create_online' => 'Create CV on-line',
        'button_upload' => 'Upload',
        'button_upload_cv' => 'Upload CV',
        'button_download_cv_template' => 'Download CV Templates',
        'template_id' => 'CV Template',
        'choose_template' => 'Choose template',
        
        'add_info' => 'Additional Information',
        'proff_skills' => 'Professional Skills',
        Lang::ERROR => array(
            'empty' => 'Field is required',
            'wrong' => 'Incorrect',
            'short' => 'Value too short. Must be more than {length}',
            'long' => 'Value too long. Must be less than {length}',
            'unique' => 'Field is unique',
            'mismatch' => 'Values not match. {field1} and {field2} must be equal.',
            'string' => 'Field should be a string',
            'less' => 'Value less',
            'wysiwyg' => 'Field should contain the content',
        )
    ),
    'position' => array(
        'title' => 'Job Title',
        'facility' => 'Company name',
    ),
    'education' => array(
        'degree' => 'Degree',
        'facility' => 'Education establishment',
        'from' => 'From',
        'to' => 'To',
        'comments' => 'Comments',
    ),
    'work' => array(
        'no_work' => 'I have no work experience',
        'title' => 'Job Title',
        'employer' => 'Employer',
        'brif' => 'Brief description',
        'from' => 'From',
        'to' => 'To',
        'phone' => 'Phone',
        'achievment' => 'Achievments',
    ),
    'candc' => array(
        'title' => 'Job Title',
        'facility' => 'Organization',
        'from' => 'From',
        'to' => 'To',
        'comments' => 'Comments'
    ),
    'language' => array(
        'name' => 'Language',
        'knowledge' => 'Language knowledge',
        'comments' => 'Comments',
    ),
);

/* End of file cv.php */
/* Location: ./lang/en/cv.php */
?>