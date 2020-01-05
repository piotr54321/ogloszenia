<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Controller{

	function  __construct(){
		parent::__construct();

		$this->load->library('paypal_lib');
		$this->load->model('product');
		$this->load->model('walletmodel');
	}

	function ipn(){
		$paypalInfo = $this->input->post();
		if(!empty($paypalInfo)){
			$ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

			if($ipnCheck){
				$data['user_id'] = $paypalInfo["custom"];
				$data['product_id'] = $paypalInfo["item_number"]; //ID_CURRENCY
				$data['txn_id'] = $paypalInfo["txn_id"];
				$data['txn_id'] = $paypalInfo["txn_id"];
				$data['payment_gross'] = $paypalInfo["mc_gross"];
				$data['currency_code'] = $paypalInfo["mc_currency"];
				$data['payer_email'] = $paypalInfo["payer_email"];
				$data['payment_status'] = $paypalInfo["payment_status"];

				//TODO ??
				$this->walletmodel->walletUpdate(['operation' => true, 'id_user' => $data['user_id'], 'id_currency' => $data['product_id'], 'amount' => $data['payment_gross']]);
			}
		}
	}
}
