<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model Page
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('StaticCommon', false);

/**
 * class modelPage
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelPage extends modelStaticCommon
{
    protected function setValidationRules()
    {
        return array();
    }
    public function getPrivateForMainMenu()
    {
        return $this->get(array('parent_id'=>0,'type'=>'private'));
    }
    
    public function getGuide($parent)
    {
        $parent_id = $this->getOne('id', array('url'=>$parent,'type'=>'guide'));
        $res = $this->get(array('parent_id'=>$parent_id,'type'=>'guide'));
        $guide = array();
        foreach($res as $k => &$v)
        {
            $guide[$v['url']] = $v;
        }
        unset($v);
        return $guide;
    }
}

/* End of file Page.php */
/* Location: ./models/frontend/Page.php */
?>