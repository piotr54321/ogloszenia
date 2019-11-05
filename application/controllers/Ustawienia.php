<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 11.03.2019
 * Time: 09:54
 */

class Ustawienia extends AC_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
    }

    function index(){
		$data = $this->pageData();
		$this->twig->display('settings/index.html', $data);
    }

    function adresy(){
		$data = $this->pageData();
		$this->twig->display('settings/adresy.html', $data);
    }

    function logowanie(){
		$data = $this->pageData();
		$this->twig->display('settings/logowanie.html', $data);
    }

    function daneosobowe(){
		$data = $this->pageData();
		$this->twig->display('settings/daneosobowe.html', $data);
    }
}
