<?php if (!defined('APP')) exit('No direct script access allowed');
/**
 * Component
 * @author ynedostup
 * @filesource
 */

Loader::loadExtension('Paypal');

class PaypalComponent extends CommonController {

    protected $model = null;

    public $data = null;

    protected $_paypal = null;

    protected $_session = 'paypal';

    protected function _init()
    {
        Paypal::init();
        $this->model=App::model('PaypalCommon',false);
        $this->_paypal=Paypal::getInstance();
    }

    protected function createControllerModel($model, $site_part = true)
    {
    }

    protected function setDefaultBreadCrumb()
    {
    }

    protected function setDefaultSiteTitle()
    {

    }


    protected function setExpressCheckout($plan){
        $params=array(
            'PAYMENTREQUEST_0_AMT'=>urlencode($plan['instant_cost']),
            'PAYMENTREQUEST_0_PAYMENTACTION'=>urlencode('Authorization'),
            'RETURNURL' => $this->base_url.'/paypal/success',
            'CANCELURL' => $this->base_url.'/paypal',
            'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode('GBP'),
            'L_BILLINGTYPE0' => urlencode('RecurringPayments'),
            'L_BILLINGAGREEMENTDESCRIPTION0' => 'Jobcentrepod membership payment',
            'PAYMENTREQUEST_0_DESC' => 'Jobcentrepod membership payment',
        );
        $this->_paypal->addFields($params);
        $response = $this->_paypal->request('SetExpressCheckout');
        if (strtoupper($response['ACK'])=='SUCCESS'){
            $token=urldecode($response['TOKEN']);
            header('Location: '.$this->_paypal->getPaypalUrl().'?cmd=_express-checkout&token='.urlencode($token));
            return true;
        } else {
            return false;
        }
    }

    protected function setSession($name,$value){
        $_SESSION[$this->_session][$name]=$value;
    }

    protected function getSession($name){
        if (isset($_SESSION[$this->_session][$name])){
            return $_SESSION[$this->_session][$name];
        } else {
            return false;
        }
    }

    protected function validate()
    {
        $this->data=$this->_request->getPost('data','',Request::FILTER_ARRAY);
        return parent::validate();
    }



    public function indexAction(){
        $model=App::model('PaypalCommon',false);
        $payment_plan=$model->getPaypalPlan();
        $this->_view->payment_plan=$payment_plan;

         if ($this->_request->isPost()){
            $this->data=$this->_request->getPost('data','',Request::FILTER_ARRAY);
            if ($this->validate()){
                $plan_id=$this->data['describe'];
                $plan=$model->getPaypalPlan($plan_id);
                $this->setSession('plan',$plan);
                if (!$this->setExpressCheckout($plan)){
                    $this->_view->addError($this->_lang->paypal()->error()->paypal_error);
                   // $this->redirect('paypal');
                }
            }
        }
    }

    protected function getExpressCheckoutDetails($token){

        $this->_paypal->addField('TOKEN',$token);
        $response = $this->_paypal->request('GetExpressCheckoutDetails');
      //  show($response); exit;
        if (strtoupper($response['ACK'])=='SUCCESS'){
            return $response;
        } else {
            return false;
        }
    }

    protected function doExpressCheckoutPayment($params){
        $this->_paypal->addFields($params);
        $response = $this->_paypal->request('DoExpressCheckoutPayment');
        //show($response); exit;
        if (strtoupper($response['ACK'])=='SUCCESS'){
            return $response;
        } else {
            return false;
        }
    }

    protected function createRecuringPaymentsProfile($params){
        $this->_paypal->addFields($params);
        $response = $this->_paypal->request('CreateRecurringPaymentsProfile',$params);
        show($response); exit;
        if (strtoupper($response['ACK'])=='SUCCESS'){
            return $response;
        } else {
            return false;
        }
    }

    public function successAction(){
        if( isset($_GET['token']) && !empty($_GET['token']) && isset($_GET['PayerID']) && !empty($_GET['PayerID'])) {
            $token=$_GET['token'];
            $payerId=$_GET['PayerID'];
            $plan=$this->getSession('plan');

            $checkoutDetails = $this->getExpressCheckoutDetails($token);
            $this->setText($checkoutDetails,'getExpressCheckoutDetails');
            if ($checkoutDetails!=false){
                $checkoutPaymentParams=array(
                    'TOKEN'=>$_GET['token'],
                    'PAYERID'=>$_GET['PayerID'],
                    'PAYMENTREQUEST_0_PAYMENTACTION'=>urlencode('Sale'),
                    'PAYMENTREQUEST_0_AMT'=>urlencode('11.00'),
                    'PAYMENTREQUEST_0_DESC' => 'Testing PayPal recurring',
                    'PAYMENTREQUEST_0_CURRENCYCODE'=>urlencode('GBP'),
                    'L_BILLINGTYPE0' => urlencode('RecurringPayments'),
                    'L_BILLINGAGREEMENTDESCRIPTION0' => 'SamePayments',

                );
                $checkoutPayment=$this->doExpressCheckoutPayment($checkoutPaymentParams);
                $this->setText($checkoutPayment,'doExpressCheckoutPayment');
                if ($checkoutPayment!=false){
                    $recurringParams = array(
                        'TOKEN' => $token,
                        'AMT' => urlencode('6.00'),
                        'CURRENCYCODE' => urlencode('GBP'),
                        'PROFILESTARTDATE' => gmdate("Y-m-d\TH:i:s\Z"),
                        'BILLINGPERIOD' => urlencode('Month'),
                        'BILLINGFREQUENCY' => urlencode('1'),
                        'TRIALBILLINGPERIOD' => 'Month',
                        'TRIALBILLINGFREQUENCY'=>'3',
                        'TRIALTOTALBILLINGCYCLES'=>'1',
                        'TRIALAMT'=>'0.00',
                        'DESC' => urlencode('SamePayments'),
                        'L_BILLINGTYPE0' => urlencode('RecurringPayments'),
                        'L_BILLINGAGREEMENTDESCRIPTION0' => urlencode('SamePayments'),
                    );
                    $createRecurring=$this->createRecuringPaymentsProfile($recurringParams);
                    $this->setText($createRecurring,'createRecuringPaymentsProfile');
                    show($createRecurring); exit;
                } else {
                    $this->_view->addErrorToSession($this->_lang->paypal()->error()->paypal_error);
                    $this->redirect('paypal');
                }
            } else {
                $this->_view->addError($this->_lang->paypal()->error()->paypal_error);
                $this->redirect('paypal');
            }

        } else {
            $this->_view->addError($this->_lang->paypal()->error()->paypal_error);
            $this->redirect('paypal');
        }
    }

    public function paypalAction(){
        $model=App::model('PaypalCommon',false);
        $payment_plan=$model->getPaypalPlan();
        $this->_view->setTemplate('index');
        $this->_view->payment_plan=$payment_plan;

         if ($this->_request->isPost()){
            $this->data=$this->_request->getPost('data','',Request::FILTER_ARRAY);
            if ($this->validate()){
                $plan_id=$this->data['describe'];
                $plan=$model->getPaypalPlan($plan_id);
                $this->setSession('plan',$plan);
                if (!$this->setPayment($plan)){
                    $this->_view->addError($this->_lang->paypal()->error()->paypal_error);
                   // $this->redirect('paypal');
                }
            }
        }
    }

    public function setCheckout(){
        $params=array(
            'RETURNURL' => $this->base_url.'/paypal/result',
            'CANCELURL' => $this->base_url.'/paypal/paypal',
            //'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
            'PAYMENTREQUEST_0_DESC' => 'Testing PayPal recurring',
            'PAYMENTREQUEST_0_NOTIFYURL' => 'http://barton.netai.net/ipn.php',
            'L_BILLINGTYPE0' => 'RecurringPayments',
            'L_BILLINGAGREEMENTDESCRIPTION0' => 'SamePayments',
            'MAXAMT' => '100',
            //'NOSHIPPING'=>'1'
        );
        $this->_paypal->addFields($params);
        $response = $this->_paypal->request('SetExpressCheckout');
        $this->setText($response,'SetExpressCheckout');
        if (strtoupper($response['ACK'])=='SUCCESS'){
            $token=$response['TOKEN'];
            header('Location: '.$this->_paypal->getPaypalUrl().'?cmd=_express-checkout&token='.$token);
            return true;
        } else {
            return false;
        }
    }

    public function resultAction(){
        $this->_view->setTemplate('response');
         if (isset($_GET['token'])){
            $this->_paypal->addFields(array('TOKEN'=>$_GET['token']));
            $response=$this->_paypal->request('GetExpressCheckoutDetails');
             $this->setText($response,'GetExpressCheckoutDetails');
            if ($response['ACK']=='Success'){
                $recurring=array(
                    'TOKEN' => $_GET['token'],
                    'AMT' => urlencode('5.00'),
                    'CURRENCYCODE' => urlencode('GBP'),
                    'PROFILESTARTDATE' => gmdate("Y-m-d\TH:i:s\Z"),
                    'BILLINGPERIOD' => urlencode('Month'),
                    'BILLINGFREQUENCY' => urlencode('1'),
                    'TRIALBILLINGPERIOD' => 'Month',
                    'TRIALBILLINGFREQUENCY'=> '2',
                    'TRIALTOTALBILLINGCYCLES'=>'1',
                    'TRIALAMT'=> '0.00',
                    'DESC' => urlencode('SamePayments'),
                    'L_BILLINGTYPE0' => urlencode('RecurringPayments'),
                    'L_BILLINGAGREEMENTDESCRIPTION0' => urlencode('SamePayments'),
                    'INITAMT' => urlencode('10.00'),
                    'NOTIFYURL' => 'http://barton.netai.net/ipn.php',
                );
                $this->_paypal->addFields($recurring);
                $response=$this->_paypal->request('CreateRecurringPaymentsProfile');
                $this->setText($response,'CreateRecurringPaymentsProfile');
                if ($response['ACK']=='Success'){
                    $this->_paypal->addFields(array('ProfileID'=>$response['PROFILEID']));
                    $profile=$this->_paypal->request('GetRecurringPaymentsProfileDetails');
                    show($profile); exit;
                }
            }
         }
    }


    public function setPayment($plan){
            $params = array(
                'PAYMENTREQUEST_0_AMT' => '11.00',
                'RETURNURL' => $this->base_url.'/paypal/success',
                'CANCELURL' => $this->base_url.'/paypal/paypal',
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'GBP',
                'PAYMENTREQUEST_0_DESC' => 'Testing PayPal recurring',
                'PAYMENTREQUEST_0_NOTIFYURL' => 'http://barton.netai.net/ipn.php',
                'L_BILLINGTYPE0' => 'RecurringPayments',
                'L_BILLINGAGREEMENTDESCRIPTION0' => 'SamePayments'
            );
        $this->_paypal->addFields($params);
        $response = $this->_paypal->request('SetExpressCheckout');
        if (strtoupper($response['ACK'])=='SUCCESS'){
            $token=$response['TOKEN'];
            header('Location: '.$this->_paypal->getPaypalUrl().'?cmd=_express-checkout&token='.$token);
            return true;
        } else {
            return false;
        }
    }


    public function ipnAction()
    {
        $arr=array(
            'name' => 'Yuriy',
            'age'  => '22'
        );
        $str="Request by date: ".date('y-m-d H:i:s')."\r\n";
        $r=(fopen(App::request()->getBaseDir().DS.'gallery'.DS.'test.txt','a+'));
        foreach ($arr as $k=>$v){
            $str.="    ".$k." = ".$v."\r\n";
        }
        $str.="End of request\r\n";
        fwrite($r,$str);
        fclose($r);
        $this->_view->setTemplate('index');
    }

    protected function setText($arr,$mode=''){
        $str="Request by date: ".date('y-m-d H:i:s')."\r\n";
        $str.="  -".$mode." METHOD:\r\n";
        $r=(fopen(App::request()->getBaseDir().DS.'gallery'.DS.'testResponse.txt','a+'));
        if (is_array($arr)){
            foreach ($arr as $k=>$v){
                $str.="    ".$k." = ".$v."\r\n";
            }
        } else {
            $str.="Error\r\n";
        }
        $str.="End of request\r\n";
        fwrite($r,$str);
        fclose($r);
    }

}

