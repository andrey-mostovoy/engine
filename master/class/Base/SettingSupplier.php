<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Interface
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

/**
 * Interface for suppliers
 */
interface SettingSupplier
{
    /**
     * Fills Settings container with specific settings
     * Called each time when related with supplier option not found
     * 
     * Useful functions that might be used inside:
     * App::setting()->getParams()  - params set by App::setting()->params()
     * App::setting()->getRequest() - part of option request
     * Example of getRequest():
     *  request 'section/subsection/option_name/suboption', 
     *  supplier registered for 'section/subsection'
     *  getRequest() returns array(0 => 'option_name', 1 => 'suboption'), 
     *      because 'section/subsection' already must be known for supplier
     * 
     * Also settings from other sections might be used
     * @example
     *  public function settingLoad()
     *  {
     *      $profileType = App::setting()->getParams('profile_type');
     *      $default     = App::setting('mysdtn/default/about')->option($profileType);
     *
     *      App::setting('mysdtn/about')->add($default);
     *  }
     * IMPORTANT: do not select settings that current supplier's responsible by App::setting().
     * If option is "null" recursion will happen.
     */
    public function settingLoad();
}