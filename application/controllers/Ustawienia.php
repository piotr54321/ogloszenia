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
		$this->load->model('addressesmodel');
		$this->load->model('loginmodel');
		$this->load->library('form_validation');
		$this->load->helper('security');
		if (!isset($data)) {
			$data = [];
		}
		$this->data = array_merge($this->page_data(), $data, $this->session->flashdata());
	}

	function index()
	{
		$this->twig->display('settings/index.html', $this->data);
	}

	function adresy()
	{
		$this->data['user_adressess'] = $this->addressesmodel->adressesFind(['id_user' => $this->data['user']['id']]);
		//var_dump($this->data['user_adressess']);
		$this->twig->display('settings/adresy.html', $this->data);
	}

	function adres_edytuj($address_id = null)
	{
		if (is_numeric($address_id)) {
			$this->data['address_edited'] = $this->addressesmodel->adressesFind(['id_user' => $this->data['user']['id'], 'id_address' => $address_id])[0];
		}
		if (!is_numeric($address_id) || $this->data['address_edited'] == false) {
			$this->data['error'] = 'Nieprawidłowe ID adresu';
		} else {
			//var_dump($this->data['address_edited']);
			if ($this->input->post()) {
				$this->form_validation->set_rules('name', 'name', 'trim|xss_clean');
				$this->form_validation->set_rules('city', 'city', 'trim|xss_clean');
				$this->form_validation->set_rules('street', 'street', 'trim|xss_clean');
				$this->form_validation->set_rules('home', 'home', 'trim|xss_clean');
				$this->form_validation->set_rules('postal_code', 'postal_code', 'trim|xss_clean');
				$this->form_validation->set_rules('main_address', 'main_address', 'trim|xss_clean');
				$this->form_validation->set_rules('addressID', 'addressID', 'trim|xss_clean|required');

				if ($this->form_validation->run() == false) {
					$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
					redirect('/ustawienia/adres_edytuj/' . $address_id, 'location');
				} else {
					$data_address['update'] = [
						'name' => $this->input->post('name'),
						'city' => $this->input->post('city'),
						'street' => $this->input->post('street'),
						'home' => $this->input->post('home'),
						'postal_code' => $this->input->post('postal_code'),
						'main_address' => $this->input->post('main_address')
					];
					$data_address['where'] = [
						'id_user' => $this->data['user']['id'],
						'id_address' => $this->input->post('addressID')
					];

					if ($this->addressesmodel->addressUpdate($data_address)) {
						$this->session->set_flashdata('complete', 'Poprawnie zmieniono dane adresu');
						redirect('/ustawienia/adres_edytuj/' . $address_id, 'location');
					} else {
						$this->session->set_flashdata('error', 'Niepowodzenie');
						redirect('/ustawienia/adres_edytuj/' . $address_id, 'location');
					}
				}
			}
		}

		$this->twig->display('settings/adres_edytuj.html', $this->data);
	}

	function adres_dodaj()
	{
		if ($this->input->post()) {
			$this->form_validation->set_rules('name', 'name', 'trim|xss_clean|required',
				[
					'required' => 'Wprowadź nazwę (np. Imię Nazwisko lub nazwa firmy)'
				]
			);
			$this->form_validation->set_rules('city', 'city', 'trim|xss_clean|required',
				[
					'required' => 'Nie wprowadziłeś nazwy miejscowości'
				]
			);
			$this->form_validation->set_rules('street', 'street', 'trim|xss_clean|required',
				[
					'required' => 'Nie wprowadziłeś nazwy ulicy'
				]
			);
			$this->form_validation->set_rules('home', 'home', 'trim|xss_clean|required',
				[
					'required' => 'Nie wprowadziłeś numeru domu/ lokalu'
				]
			);
			$this->form_validation->set_rules('postal_code', 'postal_code', 'trim|xss_clean|required',
				[
					'required' => 'Nie wprowadziłeś kodu pocztowego'
				]
			);
			$this->form_validation->set_rules('main_address', 'main_address', 'trim|xss_clean|required',
				[
					'required' =>
						'Nie wskazano czy adres jest głównym adresem czy też nim nie jest'
				]
			);
			if ($this->form_validation->run() == false) {
				$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
				$this->session->set_flashdata('errors', $this->form_validation->error_array());
				redirect('/ustawienia/adres_dodaj/', 'location');
			} else {
				$data_address['insert'] = [
					'id_user' => $this->data['user']['id'],
					'city' => $this->input->post('city'),
					'name' => $this->input->post('name'),
					'street' => $this->input->post('street'),
					'home' => $this->input->post('home'),
					'postal_code' => $this->input->post('postal_code'),
					'main_address' => $this->input->post('main_address')
				];

				$addres_insert_status = $this->addressesmodel->addressInsert(
					$data_address
				);
				if ($addres_insert_status) {
					$this->session->set_flashdata('complete', 'Poprawnie dodano adres');
					redirect('/ustawienia/adres_edytuj/' . $addres_insert_status, 'location');
				} else {
					$this->session->set_flashdata('error', 'Niepowodzenie');
					redirect('/ustawienia/adres_dodaj/', 'location');
				}
			}
		}
		$this->twig->display('settings/adres_dodaj.html', $this->data);
	}

	function adres_usun()
	{
		if ($this->input->post()) {
			$this->form_validation->set_rules(
				'addressID',
				'addressID',
				'trim|xss_clean|required'
			);
			if ($this->form_validation->run() == false) {
				$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
				redirect('/ustawienia/adres_edytuj/', 'location');
			} else {
				$data_address['delete'] = [
					'id_address' => $this->input->post('addressID')
				];
				$data_address['where'] = [
					'id_user' => $this->data['user']['id']
				];
				if ($this->addressesmodel->addressDelete($data_address)) {
					$this->session->set_flashdata('complete', 'Poprawnie usunięto adres');
					redirect('/ustawienia/adresy/', 'location');
				} else {
					$this->session->set_flashdata('error', 'Niepowodzenie podczas usuwania adresu');
					redirect('/ustawienia/adres_edytuj/' . $data_address['delete']['id_address'], 'location');
				}
			}
		}
	}

	function logowanie()
	{
		$this->data['findUser'] = $this->usersmodel->getUsers([
			'id' => $this->data['user']['id']
		])[0];

		if ($this->input->post()) {
			$this->form_validation->set_rules('username', 'username', 'trim|xss_clean|is_unique[users.username]|min_length[6]',
				[
					'is_unique' => 'Podana nazwa użytkownika już istnieje',
					'min_lenght' => 'Nazwa powinna mieć przynajmniej 6 znaków'
				]
			);
			$this->form_validation->set_rules('email', 'email', 'trim|xss_clean|is_unique[users.email]|valid_email',
				[
					'is_unique' => 'Podany adres e-mail już istnieje',
					'valid_email' => 'Podany adres e-mail jest nieprawidłowy'
				]
			);

			if ($this->form_validation->run() == false) {
				$this->session->set_flashdata('info', 'Wprowadzono niepoprawne dane');
				$this->session->set_flashdata('errors', $this->form_validation->error_array());
				redirect('/ustawienia/logowanie/', 'location');
			} else {
				$user_data_set = [
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'id' => $this->data['user']['id']
				];
				if ($this->usersmodel->userUpdate($user_data_set)) {
					$this->session->set_flashdata('complete', 'Poprawnie zmieniono dane użytkownika');
					redirect('/ustawienia/logowanie/', 'location');
				} else {
					$this->session->set_flashdata('error', 'Niepowodzenie');
					redirect('/ustawienia/logowanie/', 'location');
				}
			}
		}

		$this->twig->display('settings/logowanie.html', $this->data);
	}

	function logowanie_ustaw_haslo()
	{
		$this->data['findUser'] = $this->usersmodel->getUsers([
			'id' => $this->data['user']['id']
		])[0];

		if ($this->input->post()) {
			$this->form_validation->set_rules('password_old', 'password_old', ['required', 'trim', 'xss_clean', ['password_callable', function($string){return $this->loginmodel->checkPass(['username' => $this->data['user']['username'], 'password' => $string]); }]],
				[
					'required' => 'Nie wprowadziłeś hasła',
					'password_callable' => 'Nie prawidłowe hasło'
				]
			);
			$this->form_validation->set_rules('password', 'password', 'trim|xss_clean|required|min_length[6]',
				[
					'required' => 'Nie wprowadziłeś nowego hasła',
					'min_length' => 'Hasło powinno mieć przynajmniej 6 znaków'
				]
			);
			$this->form_validation->set_rules('password2', 'password2', 'trim|xss_clean|matches[password]|required',
				[
					'required' => 'Nie wprowadziłeś nowego hasła ponownie',
					'matches' => 'Hasła nie są identyczne'
				]
			);

			if ($this->form_validation->run() == false) {
				$this->session->set_flashdata('errors', $this->form_validation->error_array());
			} else {
				$user_data_set = [
					'password' => $this->input->post('password'),
					'id' => $this->data['user']['id']
				];

				if ($this->usersmodel->userUpdate($user_data_set)) {
					$this->session->set_flashdata('complete', 'Poprawnie zmieniono hasło');
					redirect('/ustawienia/logowanie_ustaw_haslo/', 'location');
				} else {
					$this->session->set_flashdata('error', 'Niepowodzenie podczas zmiany hasła');
					redirect('/ustawienia/logowanie_ustaw_haslo/', 'location');
				}
			}
		}

		$this->twig->display('settings/logowanie_ustaw_haslo.html', $this->data);
	}

	function daneosobowe()
	{
		//TODO
		$this->twig->display('settings/daneosobowe.html', $this->data);
	}
}
