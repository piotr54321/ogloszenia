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
	 * @var array
	 */
	private $data;

	function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
        $this->load->model('categoriesmodel');
        $this->load->model('adressessmodel');
		$this->load->model('walletmodel');
        $this->load->model('adsmodel');
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
		$this->twig->display('oglszenia/index.html', $this->data);
    }

    function noweogloszenie(){

    	//TODO
		//TODO jeżeli nie ma środków to
		$this->data['user_adressess'] = $this->adressessmodel->adressessFind(['id_user' => $this->data['user']['id']]);
		$this->data['available_currencies'] = $this->walletmodel->currenciesFind(['enabled' => 1]);
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
				$dataOffer = $this->adsmodel->adInsert($dataInsert);
				if($dataOffer){
					$this->upload_files($dataOffer);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE){
					$this->session->set_flashdata('complete', 'Poprawnie dodano ogłoszenie. Oczekuje ono na akceptację administratora.');
					redirect('/ogloszenia/noweogloszenie/', 'location');
				}else{
					$this->session->set_flashdata('error', 'Niepowodzenie');
					redirect('/ogloszenia/noweogloszenie/', 'location');
				}

			}
		}

		$this->data['categories'] = $this->categoriesmodel->categoryFind(['categories.enable' => 1]);
		$this->twig->display('oglszenia/noweogloszenie.html', $this->data);
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

				var_dump($config['file_name']);
				if ($this->upload->do_upload('file')) {
					$uploadData = $this->upload->data();
					$filename = $uploadData['file_name'];
					$imageInsert['filename'] = $filename;
					$this->adsmodel->imageInsert($imageInsert);
				}
			}
		}
	}
}
