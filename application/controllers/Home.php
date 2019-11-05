<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 02.02.2019
 * Time: 21:01
 */

class Home extends AC_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
        //$this->load->library('aclsystem');
        // $this->load->model('usermodel');
    }

    public function index(){
		$data = $this->pageData();
        $this->twig->display('home/index.html', $data);
    }
}
