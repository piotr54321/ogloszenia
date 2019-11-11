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
    }

    function index(){
    	//TODO
		$data = $this->page_data();
		$this->twig->display('chat/index.html', $data);
    }

    function historia(){
    	//TODO
		$data = $this->page_data();
		$this->twig->display('chat/historia.html', $data);
    }

}
