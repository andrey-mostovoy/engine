<?php if ( ! defined('APP')) exit('No direct script access allowed');

Loader::loadController('AdminCommonController', false);

/**
 * class TransactionsController
 * Admin Transactions Module
 * @package		Project
 * @subpackage	Controllers
 * @author		ynedostup
 */
class TransactionsController extends AdminCommonController
{
    protected function _init() {
        parent::_init();
    }
    /**
     * Create table headers
     */
    protected function formTableHeader()
    {
        $this->addTableHeader(
            array(
                $this->_lang->transactions()->id,
                $this->_lang->transactions()->date,
                $this->_lang->transactions()->name,
                $this->_lang->transactions()->type,
                $this->_lang->transactions()->amount,
                $this->_lang->transactions()->status,
                $this->_lang->admin_table()->actions,
            )
        );
    }

    public function indexAction(){
        parent::indexAction();

       // Loader::loadClass('Base/Paypal');
        //$Paypal = App::paypal();

    }
}

 
