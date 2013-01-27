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

Loader::loadClass('Common_DB_ModelFilter');

/**	
 * class ModelOrder.
 * Containing common methods and class properties for work with order capability.
 * Also methods of that class used by {@see QueryBuilder}
 * 
 * @package		Base
 * @subpackage	Models
 * @category	Database
 * @author		amostovoy
 * @abstract
 */
abstract class ModelOrder extends ModelFilter
{
    /**
     * Indicate using order logic of
     * formSearch method
     * @var bool
     */
    public $use_order = true;
    /**
     * Return default order 
     * @return string
     */
    public function getDefaultOrder()
    {
        return 'id';
    }

    public function getDefaultOrderDir($type)
    {
        if(isset($this->d_orders) && isset($this->d_orders[$type]))
            return $this->d_orders[$type];
        return 'ASC';
    }
    /**
     * Format order 
     * 
     * @return array|null
     */
    protected final function formatOrder($params)
    {
        $order = array(
            'type' => isset($this->order) ? $this->order : (isset($params['order']['type']) ? $params['order']['type'] : null),
            'dir' => isset($params['order']['dir']) ? $params['order']['dir'] : null,
        );
       
        if ((empty($order['type']) || empty($order['dir']))
            && !empty($_SESSION[$params['session']['save']][Controller::ORDER])
        ){
            if (empty($order['type']))
            {
                $order = $_SESSION[$params['session']['save']][Controller::ORDER];
            }
            elseif ($order['type'] == $_SESSION[$params['session']['save']][Controller::ORDER]['type'])
            {
                $order['dir'] = $_SESSION[$params['session']['save']][Controller::ORDER]['dir'];
                $order['dir'] = ('ASC' == $order['dir']) ? 'DESC' : 'ASC';
            }
        }
        elseif (empty($order['type']))
        {
            $order['type'] = $this->getDefaultOrder();
        }
        if (empty($order['dir']))
        {
            $order['dir'] = $this->getDefaultOrderDir($order['type']);
        }

        $order['seed'] = $this->getRandomSeed('random' == $order['type']);

        if($params['session']['save'])
        {
            if(!isset(App::view()->order_by))
                 App::view()->order_by = array();
            App::view()->order_by[$params['session']['save']] = array(
                'type'  => $order['type'],
                'dir'   => $order['dir'],
            );
            return $_SESSION[$params['session']['save']][Controller::ORDER] = $order;
        }
        else
            return $order;
    }
    
    /**
     * Gets random seed
     * 
     * @param bool $needUpdate  force update
     * @return int
     */
    protected function getRandomSeed($needUpdate = false)
    {
        $sessionName = 'random_seed';
        $model = $this->getName();
        if ($needUpdate || empty($_SESSION[$sessionName][$model]))
        {
            return $_SESSION[$sessionName][$model] = mt_rand(1, 1000);
        }
        else
        {
            return $_SESSION[$sessionName][$model];
        }
    }
    
    /**
     * Forms order for "random"
     * 
     * @param string $dir
     * @return string
     */
    protected function formOrderRandom($dir, &$params)
    {
        if(empty($params['seed']))
        {
            $params['seed'] = rand(1,100);
        }
//        show($dir);
//        $seed = !empty($params['seed']) ? $params['seed'] : 0;
        return "RAND({$params['seed']}) {$dir}"; 
    }
}

/* End of file ModelOrder.php */
/* Location: ./class/Common/DB/ModelOrder.php */
?>
