<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 29.01.2019
 * Time: 21:13
 */

class LoginModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function xd(){
      
    }

    function checkPass($data = array()){
        $this->db->select('username, password');
        $this->db->from('users');
        $this->db->where('username', $data['username']);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return password_verify($data['password'], $q->result_array()[0]['password']);
        }else{
            return false;
        }
    }

    function sessionUserData($username){
        $this->db->select('id as user_id, username, email');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->limit(1);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return $q->result_array()[0];
        }else{
            return false;
        }
    }
}
