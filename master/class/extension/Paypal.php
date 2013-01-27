<?php if ( ! defined('APP')) exit('No direct script access allowed');
/**
 * @package		extension
 * @author		ynedostup
 * @copyright	Copyright (c) 2012, Qualium-Systems, Inc.
 * @version		1.0
 * @since		Version 1.0
 * @filesource
 */

/**
 * class Paypal
 * containing methods and properties for
 * work with PayPal
 *
 * @package		extension
 * @author		ynedostup
 */

class Paypal {

    protected $_errors = array();

    protected $_sandboxUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    protected $_liveUrl = 'https://www.paypal.com/cgi-bin/webscr';

    protected $_credentials = array(
        'USER'      => 'yuriy_1328624152_biz_api1.qualium-systems.com',
        'PWD'       => '1328624180',
        'SIGNATURE' => 'ACWo.fgeIR2l.qWJyTv3TW4XSyF2A1oG6h2NzkBJC.Y3JF2Hpng1BvMW',
    );

     protected $_credentials1 = array(
        'USER'      => 'seller_1329920360_biz_api1.gmail.com',
        'PWD'       => '1329920386',
        'SIGNATURE' => 'A8HBK-9si0ScoxWc8LxEPeWjoK9wA6HFgJhmD1XrQioacKEN1Eb7sbzI',
    );

    /**
    * Real - https://api-3t.paypal.com/nvp
    * Sandbox - https://api-3t.sandbox.paypal.com/nvp
    * @var string
    */
    protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * API version
     * @var string
     */
    protected $_version = 84.0;

    private static $instance = null;

    protected $_fields = array();


    public static function init(){
        //TODO: Add initialization params from DB
    }
    /**
     * Returns instance
     * @return Paypal
     */
    public static function getInstance($section = null)
    {
        self::$instance === null and self::$instance = new self();
        return self::$instance;
    }

    /**
     * Return PayPal url to request
     * @return string
     */
    public function getPaypalUrl(){
        if (Config::SANDBOX_MODE==1){
            return $this->_sandboxUrl;
        } else {
            return $this->_liveUrl;
        }
    }

    /**
     * Add field to payment request to PayPal API
     * @param $field
     * @param $value
     * @return void
     */
    public function addField($field, $value)
    {
      $this->_fields["$field"] = $value;
    }

    /**
     * Add fields to payment request to PayPal API
     * @param array $fields with $key=>$value pairs
     */
    public function addFields($fields=array())
    {
        if (is_array($fields)){
            $this->_fields = array_merge($this->_fields,$fields);
        }
    }

    /**
     * Reset fields after each request
     */
    protected function resetFields(){
        $this->_fields=array();
    }

    /**
     * Get the fields to add them to the form
     * @return array|bool
     */
    public function getFields(){
        if (empty($this->_fields)){
            return false;
        } else {
            return $this->_fields;
        }
    }

    /**
     * Return errors
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    public function addError($error)
    {
        if (!empty($error)){
            $this->_errors[]=$error;
        }
    }

    /**
     * Create request ti PayPal
     * @param $method
     * @param array $params
     * @return bool
     */
    public function request($method){
       $this->_errors=array();
        if (empty($method)){
            $this->_errors=array('Request method do not selected');
            return false;
        }

        $requestParams = array(
            'METHOD'  => $method,
            'VERSION' => $this->_version
        ) + $this->_credentials;
        $params=$this->getFields();
        $request = http_build_query($requestParams+$params);

        /*
         * cURL settings
         */
        $curlOpts = array(
            CURLOPT_URL             => $this->_endPoint,
            CURLOPT_VERBOSE         => 1,
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_CAINFO          => App::request()->getBaseDir().DS.'gallery'.DS.'cacert.pem',
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $request

        );

        $ch = curl_init();
        curl_setopt_array($ch,$curlOpts);

        $this->resetFields();

        $response = curl_exec($ch);

        //check cURL
        if (curl_errno($ch)){
            $this->addError(curl_error($ch));
            curl_close($ch);
            return false;
        } else {
            curl_close($ch);
            $responseArray=array();
            parse_str($response,$responseArray);
            return $responseArray;
        }
    }

}
