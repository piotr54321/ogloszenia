<?php

class Uzytkownicy extends AC_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('twig');
	}

	function index(){
		$data = $this->pageData();
		$this->twig->display('users/index.html', $data);
	}
}
