<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Dotpay {
    const STATUS_PENDING    = 1;
    const STATUS_SUCCESS    = 2;
    const STATUS_FAILED     = 3;
    
    const PAYMENT_URL       = 'https://ssl.dotpay.pl/';
    const PAYMENT_IP_ADDR   = '195.150.9.37';

    private $_config        = NULL;
    private $_dotpayID      = NULL;
    private $_dotpayPIN     = NULL;
    private static $_instance       = NULL;
    private $_incomingPaymentData   = NULL;
    private $_returnAction = NULL;
    
    
    private $_dataRequired = array(
        'id'                => NULL,
        'amount'            => NULL,
        'description'       => NULL,
        'control'           => NULL,
    );
    
    private $_dataOptional = array(
        'currency'          => NULL,
        'lang'              => NULL,
        'channel'           => NULL,
        'ch_lock'           => NULL,
        'online_transfer'   => NULL,
        'URL'               => NULL,
        'URLC'              => NULL,
        'type'              => NULL,
        'buttontext'        => NULL,
        'firstname'         => NULL,
        'lastname'          => NULL,
        'email'             => NULL,
        'street'            => NULL,
        'street_n1'         => NULL,
        'street_n2'         => NULL,
        'addr2'             => NULL,
        'addr3'             => NULL,
        'city'              => NULL,
        'postcode'          => NULL,
        'phone'             => NULL,
        'code'              => NULL,
        'p_info'            => NULL,
        'p_email'           => NULL,
        'tax'               => NULL,
    );

    public static function instance() {
        if (Kohana_Dotpay::$_instance === NULL)
            Kohana_Dotpay::$_instance = new Kohana_Dotpay();
        
        return Kohana_Dotpay::$_instance;
    }
	
    public function __construct() {
        $this->_config          = Kohana::$config->load('dotpay');
        $this->_dotpayID        = $this->_config->id;
        $this->_dotpayPIN       = $this->_config->PIN;
        $this->_returnAction    = $this->_config->returnAction;
    }

    public function pay($data, $clientEmail) {
        if (is_array($data))
            $this->_setDataFromArray($data);
        else if (is_object($data))
            $this->_setDataFromObject($data);
        
        $this->_dataRequired['id']      = $this->_dotpayID;
        $this->_dataOptional['email']   = $clientEmail;
        
        if (empty($this->_dataOptional['URL']))
            $this->_dataOptional['URL'] = URL::site($this->returnAction(), TRUE);
        
        foreach ($this->_dataRequired as $field)
            if (empty($field)) return FALSE;

        $fields = array_merge($this->_dataRequired, $this->_dataOptional);

        $hidden = array();
        foreach ($fields as $key => $value)
            if (!empty($value)) $hidden[$key] = $value;

        $view =  View::factory('dotpay/pay')
                ->set('amount', $this->_amountFormat($this->_dataRequired['amount']))
                ->set('description', $this->_dataRequired['description'])
                ->set('control', $this->_dataRequired['control'])
                ->set('paymentURL', self::PAYMENT_URL)
                ->bind('hidden', $hidden);
        
        if ($this->_config->selectChannel)
            $view->set('paymentChannels', $this->_config->channels);
        
        return $view;
    }
        
    public function afterPay() {
        return View::factory('dotpay/return');
    }

    public function incomingPayment() {
        if ($_POST) {
            $this->_incomingPaymentData = $_POST;
            if ($this->_checkMD5(Arr::get($_POST, 'md5')) && $this->_checkServerIP(Request::$client_ip)) {
                $payment = ORM::factory('payment', array('control' => $this->_incomingPaymentData['control']));
                if ($payment->loaded()) {
                    
                    $incomingPayment = ORM::factory('payment_incoming', array('t_id' => $this->_incomingPaymentData['t_id']));
                    if ($incomingPayment->loaded()) {
                        $incomingPayment->t_status      = $this->_incomingPaymentData['t_status'];
                        $incomingPayment->updated       = time();
                        $incomingPayment->status        = $this->_checkPayment($payment->id);
                    } else {
                        $incomingPayment->payment_id    = $payment->id;
                        $incomingPayment->t_id          = $this->_incomingPaymentData['t_id'];
                        $incomingPayment->t_status      = $this->_incomingPaymentData['t_status'];
                        $incomingPayment->amount        = $this->_amountFormat($this->_incomingPaymentData['amount']);
                        $incomingPayment->email         = $this->_incomingPaymentData['email'];
                        $incomingPayment->md5           = $this->_incomingPaymentData['md5'];
                        $incomingPayment->created       = time();
                        $incomingPayment->updated       = time();
                        $incomingPayment->status        = $this->_checkPayment($payment->id);

                        if (array_key_exists('description', $this->_incomingPaymentData))
                            $incomingPayment->description   = $this->_incomingPaymentData['description'];

                        if (array_key_exists('service', $this->_incomingPaymentData))
                            $incomingPayment->service       = $this->_incomingPaymentData['service'];

                        if (array_key_exists('code', $this->_incomingPaymentData))
                            $incomingPayment->code          = $this->_hash($this->_incomingPaymentData['code']);
                        
                        if (array_key_exists('username', $this->_incomingPaymentData))
                            $incomingPayment->username = $this->_incomingPaymentData['username'];
                        
                        if (array_key_exists('password', $this->_incomingPaymentData))
                            $incomingPayment->password = $this->_hash($this->_incomingPaymentData['password']);
                    }
                    $incomingPayment->save();
                    
                    return $incomingPayment->saved();
                }
            } else {
                $this->_incomingPaymentData = FALSE;
                Kohana_Log::instance()->add(Log::ERROR, 'Wrong MD5 hash for payment');
            }
        }
        return FALSE;
    }

    public function returnAction() {
        return $this->_returnAction;
    }
    
    private function _checkPayment($paymentID) {
        $status         = $this->_getStatus();
        $validAmount    = $this->_checkAmount($paymentID);
        
        return (boolean) ($status === Kohana_Dotpay::STATUS_SUCCESS && $validAmount);
    }
    
    private function _getStatus() {
        $status     = Arr::get($this->_incomingPaymentData, 't_status');

        $pending    = array(1);
        $success    = array(2);
        $fail       = array(3, 4, 5);

        if (in_array($status, $pending))
            return Kohana_Dotpay::STATUS_PENDING;
        
        if (in_array($status, $success))
            return Kohana_Dotpay::STATUS_SUCCESS;
        
        if (in_array($status, $fail))
            return Kohana_Dotpay::STATUS_FAILED;

        return FALSE;
    }
    private function _checkAmount($paymentID) {
        $payment = ORM::factory('payment', $paymentID);
        
        if (!$payment->loaded())
            return FALSE;
        
        return ($payment->amount === $this->_incomingPaymentData['amount']);
    }

    // Secure
    private function _checkMD5($control) {
        $data = array(
            'PIN'       => $this->_dotpayPIN,
            'id'        => Arr::get($this->_incomingPaymentData, 'id'),
            'control'   => Arr::get($this->_incomingPaymentData, 'control'),
            't_id'      => Arr::get($this->_incomingPaymentData, 't_id'),
            'amount'    => Arr::get($this->_incomingPaymentData, 'amount'),
            'email'     => Arr::get($this->_incomingPaymentData, 'email'),
            'service'   => Arr::get($this->_incomingPaymentData, 'service'),
            'code'      => Arr::get($this->_incomingPaymentData, 'code'),
            'username'  => Arr::get($this->_incomingPaymentData, 'username'),
            'password'  => Arr::get($this->_incomingPaymentData, 'password'),
            't_status'  => Arr::get($this->_incomingPaymentData, 't_status'),
        );

        return ($control === md5(implode(':', $data)));
    }
    private function _checkServerIP($ip) {
        return (Kohana_Dotpay::PAYMENT_IP_ADDR === $ip);
    } // Secure end
    private function _setDataFromArray(array $dataArray) {
        foreach ($dataArray as $key => $value) {
            if (array_key_exists($key, $this->_dataOptional))
                $this->_dataOptional[$key] = $value;

            if (array_key_exists($key, $this->_dataRequired))
                $this->_dataRequired[$key] = $value;				
        }
    }
    private function _setDataFromObject(ORM $dataObject) {
        $data = $dataObject->as_array();
        
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->_dataOptional))
                $this->_dataOptional[$key] = $value;

            if (array_key_exists($key, $this->_dataRequired))
                $this->_dataRequired[$key] = $value;				
        }
    }
    
    private function _amountFormat($amount) {
        return number_format($amount, '2', '.', '');
    }
    
    private function _hash($secretData) {
        $hashSalt = $this->_config->hashSalt;
        
        if (empty($hashSalt))
            return $secretData;
        
        return md5($hashSalt.'_'.$secretData);
    }
}
