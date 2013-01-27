<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Compressor Component
 * @filesource
 */

/**
 * class CompressorComponent
 * Compressor module. Methods for async resource compress.
 * {@uses Compressor}
 * @package		Project
 * @subpackage	Components
 */
class CompressorComponent extends CommonController
{
    protected function _commonInit() {}
    protected function _init() {}
    public function _end()
    {
        die();
    }
    protected function createControllerModel($model, $site_part = true) {}
    protected function setDefaultBreadCrumb() {}
    protected function setDefaultSiteTitle() {}
    
    
    public function indexAction()
    {
    }
    /**
     * Compress js resources. 
     */
    public function jsAction()
    {
        Loader::loadExtension('compressor.CompressorJs');
        Compressor::getInstance('js')->doCompress();
    }
    /**
     * Compress css resources. 
     */
    public function cssAction()
    {
        Loader::loadExtension('compressor.CompressorCss');
        Compressor::getInstance('css')->doCompress();
    }
}
/* End of file Compressor.php */
/* Location: ./components/Compressor.php */
?>