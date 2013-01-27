<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelPosition
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelPosition extends modelContent
{
    protected function setName()
    {
        return self::$tbl_position;
    }
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'title,facility',
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
        return $this->getRow(array('cv_id'=>$cv_id));
    }
}

/* End of file Position.php */
/* Location: ./models/Position.php */
?>
