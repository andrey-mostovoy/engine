<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		Base
 * @subpackage	Common
 * @author		amostovoy
 * @copyright	Copyright (c) 2010, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

Loader::loadClass('Common_DB_ModelPermission');

/**	
 * class ModelValidation.
 * Containing common methods and class properties for work with validation.
 * {@uses Validation}
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelValidation extends ModelPermission
{
    const EACH = '_each';
    const ITEM = '_item';
    
    /**
     * <p>Checking rules for validate.</p>
     * <p>Accept following keys:</p>
     * <table>
     *  <tr>
     *    <td>required</td>
     *    <td>- required fields, not empty</td>
     *  </tr>
     *  <tr>
     *    <td>isset</td>
     *    <td>- fields that need to have, can be empty</td>
     *  </tr>
     * </table
     * @var array
     */ 
    protected $_rules = array();
    /**
     * Checking dynamic rules for validate
     * @var array
     */ 
    protected $_dynamic_rules = array();
    
    protected function __construct()
    {
        $this->_rules = $this->setValidationRules();
        
        parent::__construct();
    }
    /**
     * Set rules for validation. For more details see
     * {@see ModelValidation::_rules}
     * @return array ruls array. If no ruls must return empty array.
     */
    abstract protected function setValidationRules();
    /**
     * <p>Check data. Loop through data array and check according to
     * validation rules. If found array in some variable will consider what
     * that is data from other model and try to check that data by other model.
     * It work recursively. It means that if checked data will containing
     * some array inside validation proccess go deep inside of these arrays.
     * Also, if current validation rules contains rule for that section than
     * validation proccess will not call related model and use own rules for 
     * section.</p>
     * {@example}
     * <p>For example we have data:
     * <code>
     * array(
     *     title => some_title
     *     address => array(
     *         city => some_sity,
     *         street => empty_street_string
     *     )
     * )
     * </code>
     * And following rules for address model:
     * <code>
     * array(
     *     required => city,street
     * )
     * </code>
     * And following rules for current model:
     * <code>
     * array(
     *     required => title
     * )
     * </code>
     * In that situation validation rules for section 'address' will take from
     * address model and will be error because street is empty.
     * But, if we declare following rules for current model:
     * <code>
     * array(
     *     required => title
     *     address => array(
     *         required => city
     *     )
     * )
     * </code>
     * All will allright, and validation pass.
     * </p>
     * @param (array|string) $data
     * @param (string) $action
     * @return (bool)
     */ 
    private function checkData(&$data, $action=null)
    {
        // collect rules
        if(isset($this->_rules['common']))
        {
            $rules = $this->_rules['common'];
        }
        else
        {
            $rules = array();
        }
        if(isset($action) && isset($this->_rules[$action]))
        {
            $rules = $this->mergeRules($rules, $this->_rules[$action]);
        }
        // replace placeholders in rules if exsist
        $this->filterRules($rules, $action, $data); 
        
        $isCorrect = true;
        // if we have one item of current model - just check it
        if(!is_numeric(key($data)))
        {
            $isCorrect = App::validation()->validate($data, $rules, true);
        }
        // if not correct, i.e. we have errors, collect errors
        if(!$isCorrect)
        {
            $this->addErrors(App::validation()->getErrors(true), 'validation');
        }
        // check data for other related models
        $isCorrect = $this->checkRelatedModelData($data, $action) && $isCorrect;

        return $isCorrect;
    }
    /**
     * Check data of related models
     * @param array $data
     * @param string $action
     * @return bool
     */
    private function checkRelatedModelData(&$data, $action=null)
    {
        $is_correct = true;
        if(!is_numeric(key($data)))
        {
            foreach($data as $k => &$d)
            {
                if(is_array($d) && !$this->isRuleExist($k, $action))
                {
                    $m = implode(array_map('ucfirst', explode('_', $k)));
                    $valid = $this->model($m, false)->validate($d, $action);
                    if(!$valid)
                    {
                        $is_correct = false;
                        $this->collectRelatedModelValidationErrors($m,$k);
                    }
                }
            }
            unset($d);
        }
        else
        {
            $all_model_errors = array();
            foreach($data as $k => &$d)
            {
                $valid = $this->validate($d, $action);
                if(!$valid)
                {
                    $this->collectRelatedModelValidationErrors(true,$k);
                    $errors = $this->getErrors(true);
                    if(!empty($errors))
                    {
                        foreach($errors as &$e)
                        {
                            array_push($all_model_errors, $e);
                        }
                        unset($e);
                    }
                }
            }
            if(!empty($all_model_errors))
            {
                $is_correct = false;
                $this->addRelatedModelErrors($all_model_errors);
            }
        }
        return $is_correct;
    }
    /**
     * Collect errors from other related model to current
     * @param string|bool $model model name or true if it is curent model
     * @param string $key 
     */
    private function collectRelatedModelValidationErrors($model,$key)
    {
        if($model === true)
            $related_model_errors = $this->getErrors(true);
        else
            $related_model_errors = $this->model($model, false)->getErrors(true);
        if(!empty($related_model_errors) && is_array($related_model_errors))
        {
            foreach($related_model_errors as &$rme)
            {
                $rme['name'] = '['.$key.']'.$rme['name'];
                array_unshift($rme['stack'], $key);
            }
            unset($rme);
            $this->addRelatedModelErrors($related_model_errors);
        }
    }
    /**
     * check if exist rule
     * @param string $rule
     * @param string $action
     * @return bool
     */
    private function isRuleExist($rule, $action)
    {
        return isset($this->_rules[$action][$rule]);
    }
    
    /**
     * Gets rules
     * 
     * @param string $action
     * @return array
     */
    public function getRules($action = null)
    {
        if ($action)
        {
            return !empty($this->_rules[$action]) ? $this->_rules[$action] : array();
        }
        
        return $this->_rules;
    }
    
    /**
     * Validate data using scenario type
     * 
     * @param array $data
     * @param string $action validation scenario $this->_rules
     * @param mixed $params (optional) (default:null) some external parameters
     * @return bool
     */ 
    public function validate($data, $action=null, $params=null)
    {
        return $this->checkData($data, $action);
    }
    
    /**
     * Merge two rules
     */ 
    private function mergeRules($rulesCommon, $rulesAdd = array())
    {
        $result = $rulesCommon;
        foreach ($result as $section => &$rules)
        {
            if (!empty($rulesAdd[$section]))
            {
                $rules .= ',' . $rulesAdd[$section];
                unset($rulesAdd[$section]);
            }
        }
        
        $result = array_merge($result, $rulesAdd);
        
        return $result;
    }
    
    /**
     * Adds dynamic data to rules
     * Replaces placeholder "{paramName}" to value with key "paramName"
     * Also add dynamic rules to check
     * 
     * @param array  $rules
     * @param string $scenario 
     */
    private function filterRules(&$rules, $scenario, $data)
    {
        $params = $this->getValidationParams($scenario, $data);
    
        if (empty($rules) || !is_array($rules) 
            || empty($params) || !is_array($params)) return;
    
        $this->formatValidationParams($params);
        $this->formatRules($rules, $params);
    }
    
    /**
     * Replaces placeholders to related values
     * 
     * @param string $rule  has placeholders
     * @param array  $params  formated '{key}'=>value pairs for replacement
     */
    private function formatRules(&$rules, &$params)
    {
        foreach ($rules as $st => &$rule)
        {
            if (is_array($rule))
            {
                $this->formatRules($rule, $params);
            }
            else 
            {
                if(array_key_exists(':'.$st.':', $params))
                {
                    $rule .= ','.ltrim($params[':'.$st.':'], '+:');
                    unset($params[':'.$st.':']);
                }
                else
                {
                    $rule = strtr($rule, $params);
                }
            }         
        }
        unset($rule);
        if(!empty($params))
        {
            foreach($params as $k => &$v)
            {
                if(strpos($k, ':') !== false)
                {
                    $rules[trim($k,':')] = ltrim($v, '+:');
                    unset($params[$k]);
                }
            }
            unset($v);
        }
    }
    /**
     * Get additional validation rules depends on data and scenario.
     * By default function retrieve dynamicly data from other models if
     * item in data array is array type.
     * @param string $scenario current validate_type (current action)
     * @param array $data data array
     * @return array|null 
     */
    protected function getDynamicRules($scenario, $data)
    {
        $dynrules = array();
        foreach($data as $k => &$v)
        {
            if(is_array($v))
            {
                $m = implode(array_map('ucfirst', explode('_', $k)));
                $rules = $this->model($m, false)->getRules($scenario);
                if(!empty($rules))
                {
                    // multiple sections
                    if(is_numeric(key($v)) && is_array(current($v)))
                    {
                        $dynrules['_each'][$k] = $rules;
                    }
                    else
                    {
                        $dynrules[$k] = $rules;
                    }
                }
            }
        }
        return $dynrules;
    }
    /**
     * Gets list of params with key=>value pairs
     * 
     * @param string $scenario  current validation scenario
     * @param array $data current data array
     * @return array|null 
     */
    protected function getValidationParams($scenario, $data)
    {
        return null;
    }
    
     /**
     * Formats params for formatRule() 
     * 
     * @param array $params 
     */
    private function formatValidationParams(&$params)
    {
        $formated = array();
        foreach ($params as $key => $value)
        {
            if(strpos($value, '+:') !== false)
            {
                $formated[':' . $key . ':'] = $value;
            }
            else
            {
                $formated['{' . $key . '}'] = $value;
            }
        }
        
        $params = $formated;
    }
    
    
    /**
     * Check field for unique.
     * 
     * @uses Validation
     * @param array $data
     * @param string $field
     * @param mixed $value
     * @param array $ad_where aditional conditions
     * @return bool return true if field exist allready (not unique),
     * false otherwise
     */
    public function validationCheckUnique($data, $field, $value, $ad_where=null)
    {
        // if validate on update we exclude from check
        // updated item
        if(isset($data['id']))
        {
            $where = array(
                'id'=>$data['id']
            );
            if(!is_null($ad_where))
            {
                $where += $ad_where;
            }
            $res = $this->getRow($where);
            if(!empty($res) && strtolower($res[$field]) == strtolower($value))
                return false;
        }
        // end
        $where = array(
            $field=>$value
        );
        if(!is_null($ad_where))
        {
            $where += $ad_where;
        }
        return (bool)$this->getRow($where);
    }
}

/* End of file ModelValidation.php */
/* Location: ./class/Common/DB/ModelValidation.php */
?>
