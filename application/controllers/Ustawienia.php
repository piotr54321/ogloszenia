<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:54
 */

class Ustawienia extends AC_Controller
{
	/**
	 * @var array
	 */
	private $data;

	function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
		$this->load->model('usersmodel');
		$this->load->model('accessmodel');
		$this->load->model('adressessmodel');
		$this->load->library('form_validation');
		$this->load->helper('security');
		if(!isset($data)){
			$data = [];
		}
		$this->data = array_merge($this->page_data(), $data, $this->session->flashdata());
    }

    function index(){

		$this->twig->display('settings/index.html', $this->data);
    }

    function adresy(){


		$this->data['user_adressess'] = $this->adressessmodel->adressessFind(['id_user' => $this->data['user']['id']]);
		//var_dump($this->data['user_adressess']);
		$this->twig->display('settings/adresy.html', $this->data);
    }

    function adres_edytuj($address_id = null){
		if(is_numeric($address_id)){
			$this->data['address_edited']=$this->adressessmodel->adressessFind(['id_user' => $this->data['user']['id'], 'id_address' => $address_id])[0];
		}
		if (!is_numeric($address_id) || $this->data['address_edited'] == FALSE){
			$this->data['error'] = 'Nieprawidłowe ID adresu';
		}else{
			//var_dump($this->data['address_edited']);
			if($this->input->post()){
				$this->form_validation->set_rules('city', 'city', 'trim|xss_clean');
				$this->form_validation->set_rules('street', 'street', 'trim|xss_clean');
				$this->form_validation->set_rules('home', 'home', 'trim|xss_clean');
				$this->form_validation->set_rules('postal_code', 'postal_code', 'trim|xss_clean');
				$this->form_validation->set_rules('main_address', 'main_address', 'trim|xss_clean');
				$this->form_validation->set_rules('addressID', 'addressID', 'trim|xss_clean|required');

				if($this->form_validation->run() == FALSE){
					$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
					redirect('/ustawienia/adres_edytuj/'.$address_id, 'location');
				}else{
					$data_address['update'] = [
						'city' => $this->input->post('city'),
						'street' => $this->input->post('street'),
						'home' => $this->input->post('home'),
						'postal_code' => $this->input->post('postal_code'),
						'main_address' => $this->input->post('main_address'),
					];
					$data_address['where'] = [
						'id_user' => $this->data['user']['id'],
						'id_address' => $this->input->post('addressID')
					];

					if($this->adressessmodel->addressUpdate($data_address)){
						$this->session->set_flashdata('complete', 'Poprawnie zmieniono dane adresu');
						redirect('/ustawienia/adres_edytuj/'.$address_id, 'location');
					}else{
						$this->session->set_flashdata('error', 'Niepowodzenie');
						redirect('/ustawienia/adres_edytuj/'.$address_id, 'location');
					}
				}
			}
		}

		$this->twig->display('settings/adres_edytuj.html', $this->data);
	}

	function adres_dodaj(){

		if($this->input->post()){
			$this->form_validation->set_rules('city', 'city', 'trim|xss_clean|required');
			$this->form_validation->set_rules('street', 'street', 'trim|xss_clean|required');
			$this->form_validation->set_rules('home', 'home', 'trim|xss_clean|required');
			$this->form_validation->set_rules('postal_code', 'postal_code', 'trim|xss_clean|required');
			$this->form_validation->set_rules('main_address', 'main_address', 'trim|xss_clean|required');
			if($this->form_validation->run() == FALSE){
				$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
				redirect('/ustawienia/adres_dodaj/', 'location');
			}else{
				$data_address['insert'] = [
					'id_user' => $this->data['user']['id'],
					'city' => $this->input->post('city'),
					'street' => $this->input->post('street'),
					'home' => $this->input->post('home'),
					'postal_code' => $this->input->post('postal_code'),
					'main_address' => $this->input->post('main_address'),
				];

				$addres_insert_status = $this->adressessmodel->addressInsert($data_address);
				if($addres_insert_status){
					$this->session->set_flashdata('complete', 'Poprawnie dodano adres');
					redirect('/ustawienia/adres_edytuj/'.$addres_insert_status, 'location');
				}else{
					$this->session->set_flashdata('error', 'Niepowodzenie');
					redirect('/ustawienia/adres_dodaj/', 'location');
				}
			}
		}
		$this->twig->display('settings/adres_dodaj.html', $this->data);
	}

	function adres_usun(){
		if($this->input->post()){
			$this->form_validation->set_rules('addressID', 'addressID', 'trim|xss_clean|required');
			if($this->form_validation->run() == FALSE){
				$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
				redirect('/ustawienia/adres_edytuj/', 'location');
			}else{
				$data_address['delete'] = [
					'id_address' => $this->input->post('addressID'),
				];
				$data_address['where'] = [
				'id_user' => $this->data['user']['id']
				];
				if($this->adressessmodel->addressDelete($data_address)){
					$this->session->set_flashdata('complete', 'Poprawnie usunięto adres');
					redirect('/ustawienia/adresy/', 'location');
				}else{
					$this->session->set_flashdata('error', 'Niepowodzenie podczas usuwania adresu');
					redirect('/ustawienia/adres_edytuj/'.$data_address['delete']['id_address'], 'location');
				}
			}
		}
	}

    function logowanie(){
		$this->data['findUser'] = $this->usersmodel->getUsers(['id' => $this->data['user']['id']])[0];


		if($this->input->post()){

		}

		$this->twig->display('settings/logowanie.html', $this->data);
    }

    function daneosobowe(){

		$this->twig->display('settings/daneosobowe.html', $this->data);
    }
}
