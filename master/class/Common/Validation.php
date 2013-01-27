<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Base_Validation');

/**
 * class Validation
 * 
 *
 * @package		Base
 * @subpackage	Common
 */
class Validation extends BaseValidation
{
    const MATCH             = 'match';
    
    /**
     * Check if fields are match
     * @example 'notequal'    => 'team1|team2',
     * @param (array) $data
     * @param (array|string) $fields
     * @return (bool)
     */
    public final function checkNotEqual($data, $fields)
    {
        $isCorrect = true;
        foreach ($fields as $field)
        {
            $pair = explode('|', $field);
            if (empty($data[$pair[0]]) && empty($data[$pair[0]])) continue;
            
            if ($data[$pair[0]] == $data[$pair[1]])
            {
                $isCorrect = false;
                $this->addError($pair[0], self::MATCH, array('field1'=>$pair[0], 'field2'=>$pair[1]));
            }
        }
        
        return $isCorrect;
    }
}

/* End of file BaseValidation.php */
/* Location: ./class/Base/Validation.php */
?>