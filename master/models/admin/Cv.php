<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('CvCommon', false);

/**
 * class modelCv
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelCv extends modelCvCommon
{
    /**
     * Checking rules e.g. 'action' => rules
     */
    protected $_rules = array(
        'common' => array(  ),
    );
    
    public function filterName($val)
    {
        return array(
            'where' => array(
                'name like' => '%'.$val.'%',
            ),
        );
    }
}

/* End of file Cv.php */
/* Location: ./models/frontend/Cv.php */
?>
