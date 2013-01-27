<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('Content', false);

/**
 * class modelLanguage
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelLanguage extends modelContent
{
    protected function setName()
    {
        return self::$tbl_language;
    }
    protected function setValidationRules()
    {
        return array(
            'common' => array(  ),
            'save' => array(
                'required' => 'name,knowledge',
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
        return $this->get(array(
                'cv_id' => $cv_id,
        ));
    }
}

/* End of file Language.php */
/* Location: ./models/Language.php */
?>
