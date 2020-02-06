<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->library('paypal_lib');
		$this->load->model('WalletModel');
		$this->load->model('HistoryModel');
	}

	function ipn()
	{
		echo "x";
		$paypalInfo = $this->input->post(); //Odebranie danych od serwera PayPal po udanej transakcji
		file_put_contents('/var/www/html/xd.txt', var_export($paypalInfo, TRUE));
		if (!empty($paypalInfo)) {
			$ipnCheck = true; //$this->paypal_lib->validate_ipn($paypalInfo); //Sprawdzenie poprawności danych
			if ($ipnCheck) {

				$data['user_id'] = $paypalInfo["custom"];
				$data['payment_gross'] = $paypalInfo["mc_gross"];
				$data['id_currency'] = $paypalInfo["item_name"];

				// Dodanie do portfelu użytkownika danej ilośći środków
				$this->WalletModel->walletUpdate(
					[
						'operation' => true,
						'id_user' => (int)$data['user_id'],
						'id_currency' => (int)$data['id_currency'],
						'amount' => ceil((double)$data['payment_gross'])
					]);
			}
		}
	}
}
