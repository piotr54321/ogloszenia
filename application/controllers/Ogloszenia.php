<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:57
 */

class Ogloszenia extends AC_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
    }

    function index(){
    	//TODO
		$data = $this->page_data();
		$this->twig->display('oglszenia/index.html', $data);
    }

    function noweogloszenie(){
    	//TODO
		$data = $this->page_data();
		$this->twig->display('oglszenia/noweogloszenie.html', $data);
    }

}
