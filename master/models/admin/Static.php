<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('StaticCommon', false);

/**
 * class modelStatic
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelStatic extends modelStaticCommon
{
    protected function setValidationRules()
    {
        return array(
            'common' => array(
                'required' => 'type,publish',
                'isset' => 'parent_id',
                'digits' => 'parent_id'
            ),
            'save_add_public' => array(
                'required' => 'title,url,content',
                'string' => 'url',
                'wordcount' => 'url:1|1',
                'wysiwyg' => 'content',
                'unique' => 'title,url'
            ),
            'save_add_private' => array(
                'required' => 'title',
                'unique' => 'title:TypeUnique'
            ),
            'save_add_private_child' => array(
                'required' => 'title,url,content',
                'wordcount' => 'url:1|1',
                'wysiwyg' => 'content',
                'unique' => 'title:TypeAndParentUnique,url'
            ),
            'save_add_guide' => array(
                'required' => 'title,url',
                'unique' => 'title:TypeUnique,url'
            ),
            'save_add_guide_child' => array(
                'required' => 'title,url,content',
                'wordcount' => 'url:1|1',
                'wysiwyg' => 'content',
                'unique' => 'title:TypeAndParentUnique,url'
            ),
        );
    }
    
    protected function _init()
    {
        parent::_init();
        $this->_rules['save_edit_public'] = $this->_rules['save_add_public'];
        $this->_rules['save_edit_private'] = $this->_rules['save_add_private'];
        $this->_rules['save_edit_private_child'] = $this->_rules['save_add_private_child'];
        $this->_rules['save_edit_guide'] = $this->_rules['save_add_guide'];
        $this->_rules['save_edit_guide_child'] = $this->_rules['save_add_guide_child'];
    }
    
    public function validationCheckTypeUnique($data, $field, $value, $ad_where=null)
    {
        return $this->validationCheckUnique($data, $field, $value, array(
            'type' => $data['type']
        ));
    }
    public function validationCheckTypeAndParentUnique($data, $field, $value, $ad_where=null)
    {
        return $this->validationCheckUnique($data, $field, $value, array(
            'type' => $data['type'],
            'parent_id' => $data['parent_id']
        ));
    }
}

/* End of file Static.php */
/* Location: ./models/admin/Static.php */
?>