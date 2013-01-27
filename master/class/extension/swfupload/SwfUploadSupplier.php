<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Interface
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @author      Andrey Mostovoy
 * @filesource
 */

/**
 * Interface for Swf file upload suppliers
 * @author      Andrey Mostovoy
 */
interface SwfUploadSupplier
{
    /**
     * Some additional actions before upload and check upload actions.
     * Might be usefull iside:
     * {@see SwfUpload::handleError()} - send error text to response
     * IMPORTANT: must return true on success or false on fail
     */
    public function swfUploadBeforeUpload();
    /**
     * Additional checks for uploaded file
     * Might be usefull iside:
     * {@see SwfUpload::FILE_DATA} - key in FILES array for uploaded file
     * {@see SwfUpload::handleError()} - send error text to response 
     * IMPORTANT: must return true on success or false on fail
     */
    public function swfUploadCheck();
    /**
     * Actions before save proccess
     * Might be usefull iside:
     * {@see SwfUpload::getTmpDir()} - Get temporary directory
     * {@see SwfUpload::handleError()} - send error text to response
     * IMPORTANT: must return true on success or false on fail 
     * @param string $file_name file name to save
     * @param string $dir part of directory path to save in, i.e. image
     */
    public function swfUploadBeforeSave($file_name, $dir);
    /**
     * Actions after save proccess
     * Might be usefull iside:
     * {@see SwfUpload::handleError()} - send error text to response
     * IMPORTANT: must return true on success or false on fail 
     * @param string $file_name file name to save
     * @param string $dir part of directory path to save in, i.e. image
     */
    public function swfUploadAfterSave($file_name, $dir);
}