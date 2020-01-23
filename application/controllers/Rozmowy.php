<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:56
 */

class Rozmowy extends AC_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('twig');
		$this->load->model('ChatModel');
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
		$this->data['segments'] = [
			'user_id' => $id_user = $this->uri->segment(3, 0),
			'offer_id' => $offer_id = $this->uri->segment(4, 0)
		];

		$this->data['aktualne_rozmowy'] = $this->ChatModel->getChats(['where' => ['offers.id_user' => $this->data['user']['id']]]);
		$this->twig->display('chat/index.html', $this->data);
	}

	function historia()
	{
		$data = $this->page_data();
		$this->twig->display('chat/historia.html', $data);
	}

	function get_messeges()
	{ // Metoda odpowiedzialna za wyświetlenie wiadomości w formacie JSON
		$id_user = $this->uri->segment(3, 0);
		$offer_id = $this->uri->segment(4, 0);
		header('Content-Type: application/json');
		$messeges = json_encode($this->ChatModel->getMesseges([
			'where' => ['msgs.id_user' => $id_user, 'msgs.id_offer' => $offer_id],
			'limit' => 20
		]));
		$this->output->enable_profiler(FALSE);
		echo $messeges;
	}

	function post_message()
	{ // Metoda odpowiedzialna za zapis wiadomości oraz potwierdzenia zapisu w formacie JSON
		$reply = $this->data['user']['id'] == $this->input->post('user_id') ? 0 : 1;
		$msg_data = [
			'id_offer' => $this->input->post('offer_id'),
			'id_user' => $this->input->post('user_id'),
			'text' => $this->input->post('msg'),
			'reply' => $reply
		];
		echo json_encode($this->ChatModel->saveMessage($msg_data));
	}
}
