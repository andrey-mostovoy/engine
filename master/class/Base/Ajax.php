<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @filesource
 */

/**
 * class BaseAjax
 * containing methods to deel with ajax response format
 * @todo take care of this class
 * @package		Base
 * @subpackage	Extension
 * @author		amostovoy
 */
class BaseAjax
{
    const RESULT_OK = 'ok';
    const RESULT_ERROR = 'error';
    const RESULT_MESSAGE = 'message';
    const RESULT_CONFIRM = 'confirm';
    const RESULT_REDIRECT = 'redirect';
    const RESULT_HTML = 'html';
    const RESULT_EMPTY = 'empty';

    private $result = '';
    private $content = '';
    private $response = array(); 

    public function __construct($result=null, $content=null)
    {
        $this->set($result, $content);
    }

    public final function set($result=null, $content=null)
    {
        $this->setResult($result);
        $this->setContent($content);
    }

    private function sendHeaders()
    {
        header('Content-type: application/json');
    }
    
    public function send($result=null, $content=null)
    {
        if(!empty($result) || !empty($content))
        {
            $this->set($result, $content);
        }
        $this->formResponse();

        $this->sendHeaders();
		echo json_encode($this->response);
        die();
    }
    
    public function sendJsonContent($content = null)
    {
        $this->sendHeaders();
		echo json_encode($content);
        die();
    }
    public function sendContent($content = null)
    {
		echo $content;
        die();
    }

    public final function setResult($result)
    {
        if(!empty($result))
        {
            $this->result = $result;
        }
    }

    public final function setContent($content)
    {
        if(!empty($content))
        {
            $this->content = $content;
        }
    }

	private function formResponse()
	{
        $this->response = array(
            'result'    => $this->result,
            'content'   => $this->content
        );
	}
}

/* End of file Ajax.php */
/* Location: ./class/Base/Ajax.php */
?>