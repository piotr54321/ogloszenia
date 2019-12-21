<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 02.02.2019
 * Time: 17:05
 */

class AC_Controller extends CI_Controller{
    private $classMethod;

    function __construct()
    {
        parent::__construct();
        $this->load->library('aclsystem');
        $this->load->library('session');
        $class = $this->router->class;
        $method = $this->router->method;
        $this->classMethod = $class.':'.$method;
        $this->output->enable_profiler(TRUE);
        $this->pageDisallowWhenLogged();
        $this->accessToPage();
    }

    function pageDisallowWhenLogged(){
        if($this->session->user_id){
            $x = ['login:index', 'login:register'];
            if(in_array($this->classMethod, $x)){
                show_404();
            }
        }
    }

	/**
	 * 
	 */
	function accessToPage(){
        if(!$this->aclsystem->checkResourceAccess($this->classMethod)){
            show_404();
        }
    }

	/**
	 * @return array|false
	 */
	function page_data(){
		$data = [];
		$user = $this->usermodel->getUser($this->session->user_id);
		$user_rank = $this->aclsystem->getAllUserRanksList;
		$data['dostepneStrony'] = array_values($this->aclsystem->allResourcesNames);
		$data['nazwaRangi'] = array_column($user_rank, 'rolename')[(array_key_last(array_column($user_rank, 'rolename')))];

		$data['user'] = $user;
		return $data;
	}
}
