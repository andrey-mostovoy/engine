<?php if ( ! defined('APP')) exit('No direct script access allowed');

Loader::loadModel('Content', false);

class modelTransactions extends modelContent
{
    protected function setName()
    {
        return self::$tbl_payment;
    }
    protected function setValidationRules()
    {
        return array();
    }
}
 
