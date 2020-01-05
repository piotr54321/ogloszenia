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

    function index(){
    	//TODO
		$this->data['segments'] = [
			'user_id' => $id_user = $this->uri->segment(3, 0),
			'offer_id' => $offer_id = $this->uri->segment(4, 0)
		];

		$this->data['aktualne_rozmowy'] = $this->ChatModel->getChats(['where' => ['offers.id_user' => $this->data['user']['id']]]);
		//Kint::dump($this->data);
		$this->twig->display('chat/index.html', $this->data);
    }

    function historia(){
    	//TODO
		$data = $this->page_data();
		$this->twig->display('chat/historia.html', $data);
    }

    function get_messeges(){
    	$id_user = $this->uri->segment(3, 0);
		$offer_id = $this->uri->segment(4, 0);
		header('Content-Type: application/json');
    	$messeges = json_encode($this->ChatModel->getMesseges(['where' => ['msgs.id_user' => $id_user, 'msgs.id_offer' => $offer_id], 'limit' => 20]));
		$this->output->enable_profiler(FALSE);
		//Kint::dump($messeges, $this->data['user']['id']);
		echo $messeges;
	}

	function save_message(){

	}
}
