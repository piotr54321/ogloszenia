<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:57
 */

class Ogloszenia extends AC_Controller
{
	/**
	 * @var array|false
	 */
	private $data;

	function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
        $this->load->model('CategoriesModel');
        $this->load->model('AddressesModel');
		$this->load->model('WalletModel');
        $this->load->model('AdsModel');
		$this->load->library('form_validation');
		$this->load->helper('security');
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
    	//TODO
		$this->data['my_ads'] = $this->AdsModel->adsFind(['id_user' => $this->data['user']['id']]);
		//var_dump($this->data['my_ads']);
		$this->twig->display('ogloszenia/index.html', $this->data);
    }

    function noweogloszenie(){

		//TODO jeżeli nie ma środków to
		$this->data['user_adressess'] = $this->AddressesModel->adressesFind(['id_user' => $this->data['user']['id']]);
		$this->data['available_currencies'] = $this->WalletModel->currenciesFind(['enabled' => 1]);
		//var_dump($this->input->post());
		//var_dump($_FILES);
		if($this->input->post()){
			$this->form_validation->set_rules('offer_name', 'offer_name', 'trim|required|xss_clean|max_length[100]', [
				'required' => 'Nazwa jest wymagana',
				'max_lenght' => 'Maksymalna długość nazwy to 100 znaków'
			]);
			$this->form_validation->set_rules('id_category', 'id_category', 'trim|required|xss_clean', [
				'required' => 'Kategoria jest wymagana'
			]);
			$this->form_validation->set_rules('description', 'description', 'trim|required|xss_clean|max_length[1000]', [
				'required' => 'Opis jest wymagany',
				'max_lenght' => 'Maksymalna długość opisu to 1000 znaków'
			]);
			$this->form_validation->set_rules('id_address', 'id_address', 'trim|required|xss_clean', [
				'required' => 'Adres jest wymagany'
			]);
			$this->form_validation->set_rules('negotiation', 'negotiation', 'trim|xss_clean');
			$this->form_validation->set_rules('price', 'price', 'trim|xss_clean');
			$this->form_validation->set_rules('id_currency', 'id_currency', 'trim|xss_clean|required');
			if($this->form_validation->run() == FALSE){
				$this->session->set_flashdata('error', 'Błąd');
				$this->session->set_flashdata('errors', $this->form_validation->error_array());
				redirect('/ogloszenia/index/', 'location');
			}else{

				$DateTime = new DateTime('NOW');
				$oldTime = $DateTime;
				$DateTime->add(new DateInterval("P7D"));
				$dataInsert = [
					'id_category' => $this->input->post('id_category'),
					'id_user' => $this->data['user']['id'],
					'offer_name' => $this->input->post('offer_name'),
					'accept' => 0,
					'description' => $this->input->post('description'),
					'create_time' => $oldTime->format('c'),
					'end_time' => $DateTime->format('c'),
					'id_address' => $this->input->post('id_address'),
					'negotiation' => $this->input->post('negotiation'),
					'price' => $this->input->post('price'),
					'id_currency' => $this->input->post('id_currency')
				];

				$this->db->trans_begin();
				if($this->CategoriesModel->categoryFind(['categories.enable' => 1, 'categories.paid' => 1, 'id_category' => $this->input->post('id_category')])){
					if(is_array($this->WalletModel->walletsFind(['wallet.id_user' => $this->data['user']['id'], 'wallet.amount' => 5, 'currencies.currency_code' => 'PLN']))){
						$this->WalletModel->walletUpdate(['id_currency' => $this->WalletModel->currenciesFind(['currency_code' => 'PLN'])[0]['id_currency'], 'id_user' => $this->data['user']['id'], 'operation' => false, 'amount' => 5]);
					}else{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Ta kategoria jest płatna, niestety nie posiadasz nic w portfelu.');
						redirect('/ogloszenia/noweogloszenie/', 'location');
					}
				}

				$dataOffer = $this->AdsModel->adInsert($dataInsert);
				if($dataOffer){
					$this->upload_files($dataOffer);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() !== FALSE){
					$this->session->set_flashdata('complete', 'Poprawnie dodano ogłoszenie. Oczekuje ono na akceptację administratora.');
					redirect('/ogloszenia/noweogloszenie/', 'location');
				}else{
					$this->session->set_flashdata('error', 'Niepowodzenie');
					redirect('/ogloszenia/noweogloszenie/', 'location');
				}
			}
		}
		Kint::dump($this->WalletModel->walletUpdate(['id_currency' => $this->WalletModel->currenciesFind(['currency_code' => 'PLN'])[0]['id_currency'], 'id_user' => $this->data['user']['id'], 'operation' => false, 'amount' => 5]));

		$this->data['categories'] = $this->CategoriesModel->categoryFind(['categories.enable' => 1]);
		$this->twig->display('ogloszenia/noweogloszenie.html', $this->data);
    }

    function upload_files(array $dataOffer){

		$config = [
			'upload_path'=> './uploads/',
			'allowed_types' => 'jpg|png|jpeg',
			'max_size' => '5000',
		];

		$imageInsert = [
			'id_offer' => $dataOffer['insert_id'],
			'id_user' => $dataOffer['id_user']
		];

		$count = count($_FILES['files']['name']);

		for($i=0;$i<$count;$i++) {
			if (!empty($_FILES['files']['name'][$i])) {

				$_FILES['file']['name'] = $_FILES['files']['name'][$i];
				$_FILES['file']['type'] = $_FILES['files']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES['files']['error'][$i];
				$_FILES['file']['size'] = $_FILES['files']['size'][$i];

				$config['upload_path'] = 'uploads/';
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size'] = '5000';
				$config['file_name'] = $dataOffer['id_user']."l".$dataOffer['insert_id']."l";

				$this->load->library('upload', $config);

				//var_dump($config['file_name']);
				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];
					$imageInsert['filename'] = $filename;
					$this->AdsModel->imageInsert($imageInsert);
				}
			}
		}
	}

	function ad(int $adId){
		if(!is_numeric($adId)){
			$this->data['error'] = 'Nie podano ID ogłoszenia...';
		}else{
			$this->data['ad'] = $this->AdsModel->adsFind(['id_offer' => $adId])[0];
			if($this->data['ad'] != FALSE) {
				if($this->data['ad']['accept'] == 0 && @$this->data['user']['id'] != $this->data['ad']['id_user'] && in_array('ogloszenia:moderacja_ogloszeniami', $this->data['dostepneStrony'])){ //Ogłoszenie niezakceptowane -> moderator musi je zaakceptować //TODO coś innego zamiast @
					$this->data['error'] = 'To ogłoszenie nie zostało jeszcze zaakceptowane...';
				}elseif($this->data['ad']['accept'] == 1 || $this->data['user']['id'] == $this->data['ad']['id_user']){
					$this->data['ad_currency'] = $this->WalletModel->currenciesFind(['id_currency' => $this->data['ad']['id_currency']])[0];
					$this->data['ad_images'] = $this->AdsModel->imagesFind(['id_offer' => $this->data['ad']['id_offer']]);
					$this->data['ad_address'] = $this->AddressesModel->adressesFind(['id_address' => $this->data['ad']['id_address']])[0];
					$this->data['ad_category'] = $this->CategoriesModel->categoryFind(['categories.id_category' => $this->data['ad']['id_category']])[0];
					@$this->data['ad_observed'] = $this->AdsModel->observedFind(['id_user' => $this->data['user']['id'], 'id_offer' => $adId])[0];
					//Kint::dump($this->data);
					$this->AdsModel->addView($adId);
				}else{
					$this->data['error'] = 'Ogłoszenie usunięte ...';
				}
			}else{
				$this->data['error'] = 'Brak takiego ogłoszenia...';
			}
		}
		$this->twig->display('ogloszenia/ad.html', $this->data);
	}

	function moderacja_index(){

		$this->data['moderation_ads'] = $this->AdsModel->adsFind(['accept' => 0]);
		$this->twig->display('ogloszenia/moderacja/index.html', $this->data);
	}

	function moderacja_akceptuj(int $adId){
		if(!is_numeric($adId)){
			$this->data['error'] = 'Nie podano ID ogłoszenia...';
		}else{

			$dataUpdate = [
				'where' => [
					'id_offer' => $adId
				],
				'update' => [
					'accept' => 1
				]
			];
			$statusUpdate = $this->AdsModel->adUpdate($dataUpdate);
			if(!$statusUpdate){
				$this->session->set_flashdata('error', 'Ogłoszenie nie zostało zaakceptowane');
				redirect('/ogloszenia/moderacja_index/', 'location');
			}else{
				$this->session->set_flashdata('complete', 'Poprawnie zaakceptowano ogłoszenie.');
				redirect('/ogloszenia/moderacja_index/', 'location');
			}
		}
	}

	function moderacja_anuluj(int $adId){
		if(!is_numeric($adId)){
			$this->data['error'] = 'Nie podano ID ogłoszenia...';
		}else{

			$dataUpdate = [
				'where' => [
					'id_offer' => $adId
				],
				'update' => [
					'accept' => 2
				]
			];

			$statusUpdate = $this->AdsModel->adUpdate($dataUpdate);
			if(!$statusUpdate){
				$this->session->set_flashdata('error', 'Ogłoszenie nie zostało anulowane');
				redirect('/ogloszenia/moderacja_index/', 'location');
			}else{
				$this->session->set_flashdata('complete', 'Poprawnie anulowano ogłoszenie.');
				redirect('/ogloszenia/moderacja_index/', 'location');
			}
		}
	}

	function obserwuj(int $adId){
		if(!is_numeric($adId)){
			$this->data['error'] = 'Nie podano ID ogłoszenia...';
		}else{
			$dataUpdate = [
				'where' => [
					'id_offer' => $adId,
					'id_user' => $this->data['user']['id'],
				],
				'update' => [
					'id_offer' => $adId,
					'id_user' => $this->data['user']['id'],
				]
			];

			$result = $this->AdsModel->observedUpdate($dataUpdate);
			if($result){
				$this->session->set_flashdata('success', 'Ogłoszenie nie zostało anulowane');
				redirect('/ogloszenia/ad/'.$adId, 'location');
			}else{
				$this->session->set_flashdata('success', 'Poprawnie anulowano ogłoszenie.');
				redirect('/ogloszenia/ad/'.$adId, 'location');
			}
		}
	}

	function statystyki(int $adId){
		if(!is_numeric($adId)){
			$this->data['error'] = 'Nie podano ID ogłoszenia...';
		}else{
			$tablica_obserwacji = $this->AdsModel->observedFind(['id_offer' => $adId]);
			$this->data['ilosc_obserwacji'] = is_array($tablica_obserwacji) ? count($tablica_obserwacji) : 0;
			$this->data['ilosc_odpowiedzi'] = $this->AdsModel->countResponses($adId);
			$this->data['ilosc_wyswietlen'] = array_sum(array_column($this->AdsModel->viewsFind(['id_offer' => $adId]), 'counter'));
			$this->data['id_offer'] = $adId;
			//Kint::dump($this->data);
			//$this->AdsModel->viewsFind();
			$this->twig->display('ogloszenia/statystyki.html', $this->data);
		}
	}

	function edycja(){

	}

	function dane_wykresu(){
		$adId = $this->uri->segment(3, 0);
		header('Content-Type: application/json');
		$this->output->enable_profiler(FALSE);
		echo json_encode($this->AdsModel->chartData($adId));
	}
}
