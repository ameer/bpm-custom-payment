<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Cpm_BPM
{
    private $plugin_name;
    private $wsdl_url = "https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl";
    private $post_url = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';
    private $site_call_back_url =  null;
    private $terminal_id = null;
    private $username = null;
    private $password = null;
    private $edtaj = null;
    public function __construct($plugin_name, $options)
    {
        ini_set("soap.wsdl_cache_enabled", "0");

        $site_call_back_url = get_permalink($options['confirm_page_id']);
        $this->plugin_name = $plugin_name;
        $this->site_call_back_url = $site_call_back_url;
        $this->terminal_id = $options['terminal_id'];
        $this->username = $options['username'];
        $this->password = $options['password'];
        $this->edtaj = $options['edtaj'];
    }
    public function returnPostUrl()
    {
        return $this->post_url;
    }
    public function getBankToken($amount, $order_id, $customer_id, $additionalData)
    {
        $params = array(
            "terminalId"     => $this->terminal_id,
            "userName"       => $this->username,
            "userPassword"   => $this->password,
            "orderId"        => $order_id,
            "amount"         => $amount,
            "localDate"      => date("Ymd"),
            "localTime"      => date("His"),
            "callBackUrl"    => $this->site_call_back_url,
            "payerId"        => $customer_id,
            "additionalData" => $additionalData
        );
        $client = new SoapClient($this->wsdl_url);

        try {
            if ( $this->edtaj === 1 ){
            $response = $client->bpCumulativeDynamicPayRequest($params);
            } else {
             $response = $client->bpPayRequest($params);   
            }
            if ($response->return) {
                $result        = explode(',', $response->return);
                $response_code = $result['0'];
                $RefId         = $result['1'];
                return ['ok' => true, 'url' => $this->post_url, 'RefId' => $RefId, 'orderId' => $order_id, 'response_code' => $response_code];
            } else {
                $err_msg = $this->get_error_message($response->return);
                return ['ok' => false, 'msg' => $err_msg];
            }
        } catch (Exception $ex) {
            $err_msg =  $this->get_error_message('bank_connection_bpPayRequest');
            return ['ok' => false, 'msg' => $err_msg];
        }
    }
    public function verify($sale_order_id, $sale_reference_id)
    {
        // Get data from bank
        $terminal_id       = $this->terminal_id;
        $username          = $this->username;
        $password          = $this->password;
        try {
            $parameters  = array(
                'terminalId'      => $terminal_id,
                'userName'        => $username,
                'userPassword'    => $password,
                'orderId'         => $sale_order_id,
                'saleOrderId'     => $sale_order_id,
                'saleReferenceId' => $sale_reference_id
            );
            $soap_client = new soapclient($this->wsdl_url);
            $verify      = $soap_client->bpVerifyRequest($parameters);
            // Transaction verified
            if ($verify->return == 0) {
                return ['ok' => true, 'msg' => 'Verification was successful.'];
            } else {
                $error_message = $this->get_error_message($verify->return);
                return ['ok' => false, 'msg' => $error_message];
            }
        } catch (Exception $e) {
            //var_dump($e->getMessage());
            $error_message = $this->get_error_message('bank_connection_bpVerifyRequest');
            return ['ok' => false, 'msg' => $error_message];
        }
    }
    public function settle($sale_order_id, $sale_reference_id)
    {
        $terminal_id       = $this->terminal_id;
        $username          = $this->username;
        $password          = $this->password;
        try {
            $parameters = array(
                'terminalId'      => $terminal_id,
                'userName'        => $username,
                'userPassword'    => $password,
                'orderId'         => $sale_order_id,
                'saleOrderId'     => $sale_order_id,
                'saleReferenceId' => $sale_reference_id
            );
            $soap_client = new soapclient($this->wsdl_url);
            $settle     = $soap_client->bpSettleRequest($parameters);
            if ($settle->return == 0) {
                // Everything is OK!
                return ['ok' => true, 'msg' => 'Settelement was successful.'];
            } else {
                // Settle failed. lets reverse
                return ['ok' => false, 'msg' => 'Settelement was successful.'];
            }
        } catch (Exception $e) {
            //var_dump($e->getMessage());
            $error_message = $this->get_error_message('bank_connection_bpSettleRequest');
            return ['ok' => false, 'msg' => $error_message];
        }
    }
    public function reverse($sale_order_id, $sale_reference_id)
    {
        $terminal_id       = $this->terminal_id;
        $username          = $this->username;
        $password          = $this->password;
        try {
            $parameters = array(
                'terminalId'      => $terminal_id,
                'userName'        => $username,
                'userPassword'    => $password,
                'orderId'         => $sale_order_id,
                'saleOrderId'     => $sale_order_id,
                'saleReferenceId' => $sale_reference_id
            );
            $soap_client = new soapclient($this->wsdl_url);
            $reversal   = $soap_client->bpReversalRequest($parameters);
            if ($reversal->return == 0) {
                $error_message = $this->get_error_message('successful_reversal');
                return ['ok' => true, 'msg' => 'Reversal was successful.'];
            } else {
                $error_message = $this->get_error_message('failed_reversal');
                return ['ok' => false, 'msg' => $error_message];
            }
        } catch (Exception $e) {
            //var_dump($e->getMessage());
            $error_message = $this->get_error_message('bank_connection_bpReversalRequest');
            return ['ok' => false, 'msg' => $error_message];
        }
    }
    public static function get_error_message($error_code)
    {
        switch ($error_code) {
            case '0':
                return  __('Transaction was successful!', 'cpm');
                break;
            case '11':
                return __('Cart number is invalid', 'cpm');
                break;
            case '12':
                return __('Inventory is not enough', 'cpm');
                break;
            case '13':
                return __('Password is incorrect', 'cpm');
                break;
            case '14':
                return __('Too many enter password attempt', 'cpm');
                break;
            case '15':
                return __('Cart is invalid', 'cpm');
                break;
            case '16':
                return __('Too many payment', 'cpm');
                break;
            case '17':
                return __('User canceled payment', 'cpm');
                break;
            case '18':
                return __('Cart is expired', 'cpm');
                break;
            case '19':
                return __('Amount is more than valid', 'cpm');
                break;
            case '21':
                return __('Receiver is invalid', 'cpm');
                break;
            case '23':
                return __('Security error occurred', 'cpm');
                break;
            case '24':
                return __('Receiver info is invalid', 'cpm');
                break;
            case '25':
                return __('Amount is invalid', 'cpm');
                break;
            case '31':
                return __('Answer is invalid', 'cpm');
                break;
            case '32':
                return __('Info format is invalid', 'cpm');
                break;
            case '33':
                return __('Account is invalid', 'cpm');
                break;
            case '34':
                return __('Systematic error', 'cpm');
                break;
            case '35':
                return __('Date is invalid', 'cpm');
                break;
            case '41':
                return __('Res number is duplicated', 'cpm');
                break;
            case '42':
                return __('Sale transaction not found', 'cpm');
                break;
            case '43':
                return __('Verify request already sent', 'cpm');
                break;
            case '44':
                return __('Verify request not found', 'cpm');
                break;
            case '45':
                return __('Transaction is settled before', 'cpm');
                break;
            case '46':
                return __('Transaction is not settled', 'cpm');
                break;
            case '47':
                return __('Settled transaction not found', 'cpm');
                break;
            case '48':
                return __('Transaction is Reversed', 'cpm');
                break;
            case '49':
                return __('Transaction is Refunded', 'cpm');
                break;
            case '51':
                return __('Transaction is duplicated', 'cpm');
                break;
            case '54':
                return __('Transaction reference is not exist', 'cpm');
                break;
            case '55':
                return __('Transaction is invalid', 'cpm');
                break;
            case '61':
                return __('Error in withdraw', 'cpm');
                break;
            case '111':
                return __('Cart creator is invalid', 'cpm');
                break;
            case '112':
                return __('Cart creator switch error', 'cpm');
                break;
            case '113':
                return __('Cart creator has no response', 'cpm');
                break;
            case '114':
                return __('Cart owner not permitted to do this action', 'cpm');
                break;
            case '415':
                return __('Session expired', 'cpm');
                break;
            case '416':
                return __('Error in setting info', 'cpm');
                break;
            case '417':
                return __('Payer id is no valid', 'cpm');
                break;
            case '418':
                return __('Error in defining customer', 'cpm');
                break;
            case '419':
                return __('Too many enter info attempt', 'cpm');
                break;
            case '421':
                return __('IP is invalid', 'cpm');
                break;
            case 'bank_connection_bpPayRequest':
                return __('Connection to bank failed in bpPayRequest method', 'cpm');
                break;
            case 'bank_connection_bpReversalRequest':
                return __('Connection to bank failed in bpReversalRequest method', 'cpm');
                break;
            case 'bank_connection_bpSettleRequest':
                return __('Connection to bank failed in bpSettleRequest method', 'cpm');
                break;
            case 'bank_connection_bpVerifyRequest':
                return __('Connection to bank failed in bpVerifyRequest method', 'cpm');
                break;
            case 'soap':
                return __('Soap module not found', 'cpm');
                break;
            case 'settle':
                return __('Settle error', 'cpm');
                break;
            case 'successful_reversal':
                return __('Payment was failed and amount successfully reversed to customer', 'cpm');
                break;
            case 'failed_reversal':
                return __('Payment and reversal was failed', 'cpm');
                break;
            case 'order_id_existence':
                return __('Order id does not exist', 'cpm');
                break;
            case 'completed_order':
                return __('Order completed before', 'cpm');
                break;
            case 'cheating':
                return __('Returned date from bank is not the same as sent data.', 'cpm');
                break;
            case 'debug_mode':
                return __('Sorry! this payment method is no available now', 'cpm');
                break;
            default:
                return __('Unknown error', 'cpm');
        }
    }
}
