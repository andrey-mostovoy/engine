<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Component
 * @author amostovoy
 * @filesource
 */

/**
 * class CronComponent
 *
 * @package		Project
 * @subpackage	Component
 * @author		amostovoy
 */
class CronComponent extends CommonController
{
    /**
     * maximum of process running same time
     */
    const PROCESSES = 10;
    const PROCESSES_MINI_ACTION = 3;
    
    private $exit = false;
    private $pids;

    public final function __construct()
    {
        Log::addToFile('cron', 'Cron Start. Action: '.App::request()->getActionName());
        
        $this->base_dir = App::request()->getBaseDir();
        $this->domain_url = App::request()->getDomainUrl();
        $this->protected_dir = $this->base_dir.'protected'.DS;
        
        $this->base_url = App::request()->getBaseUrl();
        $this->base_address = $this->base_url.'/'.App::request()->getControllerName();
    }
    public final function _run()
    {
        $this->mediaUrls();
    }
    public final function _end()
    {
        Log::addToFile('cron', 'Cron End');
        exit();die();
    }
    protected final function _commonInit()
    {
        exec('ps -C '.basename(__FILE__).' -o pid=', $this->pids);

        // limit of running process in one time
        if (count($this->pids) > self::PROCESSES) {
            $this->exit = true;
        }
    }
    protected final function _init()
    {
        if($this->exit && count($this->pids) > (self::PROCESSES + self::PROCESSES_MINI_ACTION) )
        {
            Log::addToFile('cron', 'Running processes limit');
            $this->_end();
        }
    }
    protected function assignTemplateVariables(){}
}

/* End of file Cron.php */
/* Location: ./components/Cron.php */
?>