<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelWork
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelWork extends modelContent
{
    protected function setName()
    {
        return self::$tbl_work;
    }
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'title,employer,from_date,to_date,phone,brif,achievment',
                'address' => array(
                    'required' => 'country_id,city,address',
                )
            ),
        );
    }
    
    /**
     * makes sql alias for table lang
     */
    protected function bindTableAlias()
    {
//        $this->builder->addAlias(self::$tbl_personal, 'p');
    }
    
    public function modelAppendToCv($cv_id)
    {
        return $this->getBy(array(
            'append_after' => array(
                'address' => array(
                    $this->model('Address',false),
                    'field' => 'address_id',
                ),
            ),
            'where' => array(
                'cv_id' => $cv_id,
            )
        ));
    }
    /**
     * save information portion to personal table
     * @param type $data
     * @param type $id
     * @param type $field
     * @return type 
     */
    public function save($data, $id=null, $field='id', $foreign_key_before=null, $foreign_key_after=null)
    {
        return parent::save($data, $id, $field, array('address'=>'address_id'), $foreign_key_after);
    }
}

/* End of file Work.php */
/* Location: ./models/Work.php */
?>
