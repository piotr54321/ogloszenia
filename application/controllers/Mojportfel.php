<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:58
 */

class Mojportfel extends AC_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
    }

    function index(){
		$data = $this->page_data();
		$this->twig->display('wallet/index.html', $data);
    }

    function wplata(){
		$data = $this->page_data();
		$this->twig->display('wallet/wplata.html', $data);
    }

    function historia(){
		$data = $this->page_data();
		$this->twig->display('wallet/historia.html', $data);
    }

}
