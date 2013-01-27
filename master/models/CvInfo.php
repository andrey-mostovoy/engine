<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelCvInfo
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelCvInfo extends modelContent
{
    protected function setName()
    {
        return self::$tbl_cv_info;
    }
    
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'template_id,proff_skils',
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

/* End of file CvInfo.php */
/* Location: ./models/CvInfo.php */
?>
