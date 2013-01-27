<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.26
 * @since		Version 1.0
 * @filesource
 */

/****************************************
 *			DO NOT EDIT					*
 ****************************************/

Loader::loadClass('Base.Controller.ControllerModel');

/**
 * class ControllerView
 * 
 * main class for all controllers
 * containing basic methods and properties
 * for work with view
 * 
 * @package     Base
 * @category    Controllers
 * @author      amostovoy
 * @abstract
 */
abstract class ControllerView extends ControllerModel
{
	/**
	 * assign paging if exist and
	 * show template by calling printTemplate method
	 * of View class
	 */
	public final function showTemplate()
	{
		$this->assignTemplateVariables();
		$this->_view->printTemplate();
	}
    /**
     * Fetch Template
     * @param string $template (d:'') template fot fetch
     * @param bool $use_layout (optional d:false) indicate use layout or not
     * @return string return compiled template
     */
	public final function fetchTemplate($template='', $use_layout=false)
	{
		$this->assignTemplateVariables();
		return $this->_view->fetchTemplate($template, $use_layout);
	}
    /**
     * Fetch Element Template
     * @param string $template template fot fetch
     * @return string return compiled template
     */
	public final function fetchElementTemplate($template='')
	{
		$this->assignTemplateVariables();
		return $this->_view->fetchElementTemplate($template);
	}
	/**
	 * assign common template variables as paging, lang etc.
	 */
	protected function assignTemplateVariables()
	{
        $this->_view->user = $this->_user->getUserInfo();
        $this->_view->perm = App::perm()->get();
        App::perm()->clear();
        App::perm_container()->clear();
        
        if($this->_request->isAjax())
        {
            $this->_view->is_ajax = true;
        }
        
        $this->_view->lang = $this->_lang;
        
        $this->_view->addUrl('domain', $this->domain_url);
        $this->_view->addUrl('base', $this->base_url);
        $this->_view->addUrl('address', $this->base_address);
        $this->_view->addUrl('parts', $this->_request->getRequestUrlParts());
       
        $this->_view->addDir('base', $this->base_dir);
        $this->_view->addDir('template', $this->_view->getTemplatesDir());

        $this->_view->site_part = $this->_request->getUrl();
        $this->_view->controller = $this->_request->getControllerName();
        $this->_view->action = $this->_request->getActionName();
        
        $this->_view->addJsVar(array(
            'url' => array(
                'domain' => $this->domain_url,
                'base' => $this->base_url,
                'address' => $this->base_address
            ),
            'site_part' => $this->_request->getUrl(),
            'controller' => $this->_request->getControllerName(),
            'action' => $this->_request->getActionName(),
            'debug' => Config::DEBUG ? true : false
        ));
	}
    public function setViewFilterOrder($param)
    {
        if(isset($this->model)
            && !empty($param['session']['save']) 
            && $this->model->getName() == $param['session']['save']
        ) {
            if(!empty($param['filter']))
                $this->_view->__filter = $param['filter'];
            if(!empty($param['order']))
                $this->_view->__order = $param['order'];
        }
    }
}
/* End of file ControllerView.php */
/* Location: ./class/Base/Controller/ControllerView.php */
?>