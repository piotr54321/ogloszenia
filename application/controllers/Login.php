<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 29.01.2019
 * Time: 18:53
 */

class Login extends AC_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('twig');
		$this->load->helper('security');
		$this->load->model('usermodel');
		$this->load->model('loginmodel');
		$this->load->library('form_validation');
	}

	public function index()
	{
		$data = array();
		if ($this->input->post('submit') != null) {
			$this->form_validation->set_rules(
				'username',
				'username',
				'trim|required|xss_clean',
				array(
					'required' => 'Nie wprowadziłeś nazwy użytkownika'
				)
			);
			$this->form_validation->set_rules(
				'password',
				'password',
				'trim|required|xss_clean',
				array(
					'required' => 'Nie wprowadziłeś hasła'
				)
			);
			if (!$this->form_validation->run()) {
				$data['errors'] = $this->form_validation->error_array();
			} else {
				$dataFromForm = [
					'username' => $this->input->post('username'),
					'password' => $this->input->post('password')
				];
				if ($this->loginmodel->checkPass($dataFromForm)) {
					$data['isgood'] = 'Zalogowano';
					$this->session->set_userdata(
						$this->loginmodel->sessionUserData(
							$dataFromForm['username']
						)
					);
					redirect('/');
				} else {
					$data['errors']['other'] =
						'Błąd logowania, spróbuj jeszcze raz...';
				}
			}
		}

		$this->twig->display('login/index.html', $data);
	}

	public function register()
	{
		$data = array();
		if ($this->input->post('submit') != null) {
			$this->form_validation->set_rules(
				'username',
				'username',
				'trim|required|xss_clean|min_length[4]|max_length[30]|is_unique[users.username]|alpha_numeric',
				array(
					'required' => 'Nie wprowadziłeś nazwy użytkownika',
					'min_length' => 'Wprowadzona nazwa jest za krótka',
					'max_length' => 'Wprowadzona nazwa jest za długa',
					'is_unique' => 'Podana nazwa użytkownika jest zajęta',
					'alpha_numeric' =>
						'Wprowadzona nazwa zawiera nieprawidłowe znaki'
				)
			);
			$this->form_validation->set_rules(
				'password',
				'paswword',
				'trim|required|xss_clean|min_length[6]',
				array(
					'required' => 'Nie wprowadziłeś hasła',
					'min_length' => 'Hasło powinno mieć przynajmniej 6 znaków'
				)
			);
			$this->form_validation->set_rules(
				'password2',
				'paswword2',
				'trim|required|xss_clean|matches[password]',
				array(
					'matches' => 'Wprowadzone hasła nie są identyczne'
				)
			);
			$this->form_validation->set_rules(
				'email',
				'email',
				'trim|required|xss_clean|valid_email|is_unique[users.email]',
				array(
					'required' => 'Nie wprowadziłeś adresu email',
					'valid_email' =>
						'Wprowadzony adres email jest nieprawidłowy',
					'is_unique' => 'Wprowadzony adres email już istnieje.'
				)
			);

			if (!$this->form_validation->run()) {
				//var_dump($this->form_validation->error_array());
				$data['errors'] = $this->form_validation->error_array();
			} else {
				$dataFromForm = [
					'username' => $this->input->post('username'),
					'password' => $this->input->post('password'),
					'password2' => $this->input->post('password2'),
					'email' => $this->input->post('email')
				];
				if ($this->usermodel->userCreate($dataFromForm)) {
					$data['isgood'] =
						'Aby dokończyć rejestrację, przejdź do swojej skrzynki oraz potwierdź swój adres email';
				} else {
					$data['errors']['other'] =
						'Błąd rejestracji, spróbuj jeszcze raz...';
				}
			}
		}

		$this->twig->display('login/register.html', $data);
	}

	function testlogin()
	{
	}

	function logout()
	{
		$this->session->sess_destroy();
		redirect(site_url('/'));
	}
}
