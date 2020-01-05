<?php

class Uzytkownicy extends AC_Controller
{
	/**
	 * @var array
	 */
	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('twig');
		$this->load->model('UsersModel');
		$this->load->model('AccessModel');
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

	function index()
	{
		$this->data['allUsers'] = $this->UsersModel->getUsers();

		//var_dump($data['allUsers']);
		$this->twig->display('users/index.html', $this->data);
	}

	function edytuj($userId = null)
	{
		//var_dump($this->input->post());
		//var_dump();
		if (!is_numeric($userId)) {
			$this->data['error'] = 'Nieprawidłowe ID użytkownika';
		} else {
			$this->data['findUser'] = $this->UsersModel->getUsers([
				'id' => $userId
			])[0];
			//var_dump($data['findUser']);
			if ($this->input->post()) {
				$this->form_validation->set_rules(
					'username',
					'username',
					'trim|xss_clean|is_unique[users.username]|min_length[6]',
					[
						'is_unique' =>
							'Wprowadzona nazwa użytkownika jest już zajęta',
						'min_length' =>
							'Wprowadzony nazwa jest za krótka. Mininum to 6 znaków'
					]
				);
				$this->form_validation->set_rules(
					'email',
					'email',
					'trim|xss_clean|is_unique[users.email]|valid_email',
					[
						'is_unique' => 'Wprowadzony adres e-mail już istnieje',
						'valid_email' =>
							'Wprowadzony adres e-mail jest nieprawidłowy'
					]
				);
				$this->form_validation->set_rules(
					'banned',
					'banned',
					'trim|xss_clean'
				);
				$this->form_validation->set_rules(
					'userID',
					'userID',
					'trim|xss_clean|required'
				);

				//var_dump($this->input->post());
				if ($this->form_validation->run() == false) {
					//$this->data['info']='Wprowadzono niepoprawne dane';
					$this->session->set_flashdata(
						'info',
						'Wprowadzono niepoprawne dane'
					);
					$this->session->set_flashdata(
						'errors',
						$this->form_validation->error_array()
					);
					redirect('/uzytkownicy/edytuj/' . $userId, 'location');
				} else {
					//var_dump($this->input->post());
					$user_data_set = [
						'username' => $this->input->post('username'),
						'email' => $this->input->post('email'),
						'banned' => $this->input->post('banned'),
						'id' => $this->input->post('userID')
					];
					if ($this->UsersModel->userUpdate($user_data_set)) {
						$this->session->set_flashdata(
							'complete',
							'Poprawnie zmieniono dane użytkownika'
						);
						redirect('/uzytkownicy/edytuj/' . $userId, 'location');
					} else {
						$this->session->set_flashdata('error', 'Niepowodzenie');
						redirect('/uzytkownicy/edytuj/' . $userId, 'location');
					}
				}
			}
		}

		//var_dump($this->data);
		$this->twig->display('users/edit.html', $this->data);
	}

	function uprawnienia($userId = null)
	{
		//var_dump($this->data);
		if (!is_numeric($userId)) {
			$this->data['error'] = 'Nieprawidłowe ID użytkownika';
		} else {
			$this->data['find_user'] = $this->UsersModel->getUsers([
				'id' => $userId
			])[0];
			//var_dump($this->data['find_user']);
			$this->data['find_user_roles'] = $this->UsersModel->getUsersRoles2(
				$userId
			);
			$this->data[
				'find_user_resources'
			] = $this->UsersModel->getUserResources($userId);

			if ($this->input->post()) {
				$this->form_validation->set_rules(
					'roles[]',
					'roles',
					'trim|xss_clean|less_than[7]|integer',
					[
						'integer' => 'Nieprawidłowy wybór',
						'less_than' => 'Nieprawidłowy wybór'
					]
				);
				$this->form_validation->set_rules(
					'userID',
					'userID',
					'trim|xss_clean|required'
				);
				if ($this->form_validation->run() == false) {
					$this->session->set_flashdata(
						'info',
						'Wprowadzono niepoprawne dane'
					);
					$this->session->set_flashdata(
						'errors',
						$this->form_validation->error_array()
					);
					redirect('/uzytkownicy/uprawnienia/' . $userId, 'location');
					//var_dump('safsaf');
				} else {
					$user_data_set = [
						'roles' => $this->input->post('roles'),
						'id' => $this->input->post('userID')
					];

					if ($this->UsersModel->userRolesUpdate($user_data_set)) {
						$this->session->set_flashdata(
							'complete',
							'Poprawnie zmieniono dane użytkownika'
						);
						redirect(
							'/uzytkownicy/uprawnienia/' . $userId,
							'location'
						);
					} else {
						$this->session->set_flashdata('error', 'Niepowodzenie');
						redirect(
							'/uzytkownicy/uprawnienia/' . $userId,
							'location'
						);
					}
				}
			}
			//var_dump($data['find_user_roles']);
			//var_dump($data['find_user_resources']);
		}

		$this->twig->display('users/access.html', $this->data);
	}
}
