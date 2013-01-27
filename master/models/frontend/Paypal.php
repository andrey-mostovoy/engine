<?php if ( ! defined('APP')) exit('No direct script access allowed');

Loader::loadModel('Content', false);

class modelPaypal extends modelContent
{
    protected function setValidationRules()
    {
        return array();
    }
}
 
