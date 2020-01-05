<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:58
 */

class Mojportfel extends AC_Controller
{
	private $data;

	function __construct()
    {
		parent::__construct();
		$this->load->library('twig');
		$this->load->model('WalletModel');
		$this->load->library('form_validation');
		$this->load->helper('security');
		$this->load->library('paypal_lib');
		if (!isset($data)) {
			$data = [];
		}
		$this->data = array_merge(
			$this->page_data(),
			$data,
			$this->session->flashdata()
		);
    }

    function index(){

		$this->data['my_wallets'] = $this->WalletModel->walletsFind(['wallet.id_user' => $this->data['user']['id']]);
		if(!$this->data['my_wallets']){
			if($this->WalletModel->walletCreate(['id_user' => $this->data['user']['id'], 'amount' => 100, 'id_currency' => $this->WalletModel->currenciesFind(['currency_code' => 'PLN'])[0]['id_currency']])) { //Tworzenie pierwszego portfela o wartości 100 PLN
				$this->session->set_flashdata(
					'complete',
					'Dodano pierwszy portfel o wartości 100 PLN :)'
				);
				redirect('/mojportfel/index/', 'location');
			}else{
				$this->session->set_flashdata(
					'error',
					'Niepowodzenie przy tworzeniu portfela...'
				);
				redirect('/home/', 'location');
			}
		};

		$this->twig->display('wallet/index.html', $this->data);
    }

    function wplata(){
    	//TODO
		$paidStatus = $this->uri->segment(3, FALSE);
		if($paidStatus == 'success'){
			$this->session->set_flashdata('complete', 'Poprawnie wpłacono środki. Niedługo pojawią się one w portfelu.');
			redirect('/mojportfel/wplata/', 'location');
		}else if ($paidStatus == 'cancel'){
			$this->session->set_flashdata('error', 'Operacja wpłaty środków została anulowana');
			redirect('/mojportfel/wplata/', 'location');
		};

		$this->data['available_currencies'] = $this->WalletModel->currenciesFind(['enabled' => 1]);

		if($this->input->post('submit') == 'PayPal'){
			$this->form_validation->set_rules('id_currency', 'id_currency', 'trim|xss_clean');
			$this->form_validation->set_rules('amount', 'id_currency', 'trim|xss_clean|is_natural_no_zero');

			if($this->form_validation->run() == FALSE){
				$this->session->set_flashdata(
					'error',
					'Niepowodzenie przy tworzeniu portfela...'
				);
				$this->session->set_flashdata('errors', $this->form_validation->error_array());
				redirect('/mojportfel/wplata/', 'location');
			}else{
				$currency_code = $this->WalletModel->currenciesFind(['id_currency' => $this->input->post('id_currency')])[0]['currency_code'];
				$this->paypal_lib->add_field('return', base_url('/mojportfel/wplata/success'));
				$this->paypal_lib->add_field('cancel_return', base_url('/mojportfel/wplata/cancel'));
				$this->paypal_lib->add_field('notify_url', base_url('/paypal/ipn'));
				$this->paypal_lib->add_field('item_name', $this->input->post('id_currency'));
				$this->paypal_lib->add_field('currency_code', $currency_code);
				$this->paypal_lib->add_field('custom', $this->data['user']['id']);
				$this->paypal_lib->add_field('amount', $this->input->post('amount'));
				$this->paypal_lib->paypal_auto_form();
			}
		}
		$this->twig->display('wallet/wplata.html', $this->data);
    }

    function historia(){
    	//TODO

		$this->twig->display('wallet/historia.html', $this->data);
    }

}
