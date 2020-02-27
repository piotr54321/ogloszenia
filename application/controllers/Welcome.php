<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Passchange extends PZ_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Passchangemodel');
        $this->load->library(array('form_validation', 'session'));
        $this->load->helper(array('url', 'form'));
    }
    /*
     * $this->temp['user']['iduser']
    */
    public function index()
    {
        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('oldpassword', 'oldpassword', 'required');
            $this->form_validation->set_rules('newpassword', 'newpassword', 'required');
            $this->form_validation->set_rules('confirmpassword', 'confirmpassword', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->temp['dane'] = "gowno nie zmiana";
            } else {
                $oldpassword = $this->input->post('oldpassword');
                $newpassword = $this->input->post('newpassword');
                $confirmpassword = $this->input->post('confirmpassword');
                $login = $this->temp['user']['login']; //Nie wiem czy dobrze, ale można sprawdzić niżej w var_dump
                var_dump($login);
                if ($this->input->post('newpassword') == $this->input->post('confirmpassword')){
                    $CZY_STARE_HASLO_OK = $this->login1model->log_user('login', $oldpassword);
                    var_dump("CZY_STARE_HASLO_OK: (".$CZY_STARE_HASLO_OK.")");
                    if($CZY_STARE_HASLO_OK == TRUE){
                        $zmiana = $this->Passchangemodel->password_change($this->temp['user']['iduser'], $newpassword);
                        var_dump("Zmiana: (".$zmiana.")");
                        if($zmiana == TRUE) {
                            $this->temp['error'] = "UDAłO się";
                        }else{
                            $this->temp['error'] = "Nie udało się blad";
                        }
                    } else {
                        $this->temp['error'] = "Złe stare hasło";
                    }
                } else {
                    $this->temp['error'] = "Hasła sa inne";
                }
            }
        }
        $this->load->view('templates/header_logged', ['title'=>'VISIT']);
        $this->load->view('Sidebarview', $this->temp);
        $this->load->view('Passchangeview', $this->temp);
        $this->load->view('templates/footer');
    }

}

?>
