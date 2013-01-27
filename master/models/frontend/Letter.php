<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * Model
 * @author amostovoy
 * @filesource
 */

Loader::loadModel('LetterCommon', false);

/**
 * class modelLetter
 *
 * @package		Project
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 */
class modelLetter extends modelLetterCommon
{
    /**
     * Checking rules e.g. 'action' => rules
     */
    protected $_rules = array(
        'common' => array(  ),
    );
}

/* End of file Letter.php */
/* Location: ./models/frontend/Letter.php */
?>
