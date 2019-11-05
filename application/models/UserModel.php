<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 29.01.2019
 * Time: 21:07
 */

class UserModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function userCreate($data){
        if($this->checkRowExist('username', $data['username'])){
            return false;
        }
        if($this->checkRowExist('email', $data['email'])){
            return false;
        }
        $this->db->set('username', $data['username']);
        $this->db->set('password', $this->generatePassHash($data['password']));
        $this->db->set('email', $data['email']);
        $this->db->insert('users');
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function checkRowExist($columnName, $value){
        $this->db->where($columnName, $value);
        $this->db->from('users');
        if($this->db->count_all_results() > 0){
            return true;
        }else{
            return false;
        }
    }

    function generatePassHash($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function setLoginData($data){
        $this->db->call_function('updateLoginData', $data['user_id'], $this->generatePassHash($data['password']));
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function getUser($userId){
        if(!$this->checkRowExist('id', $userId)){
            return false;
        }
        $this->db->select('id, username, email, timeonline, timeregister');
        $this->db->from('users');
        $this->db->where('users.id', $userId);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return $q->result_array()[0];
        }else{
            return false;
        }
    }

    function getAllUserResourcesNames(){

    }

	function user($data = array()){
		if($data['username'] || $data['email']){
			$this->db->select('id, username, email, timeonline, timeregister');
			$this->db->from('users');
			if($data['username']) $this->db->where('users.username', $data['username']);
			if($data['email']) $this->db->where('users.email', $data['email']);
			$q = $this->db->get();
			if($q->num_rows() > 0){
				return $q->result_array()[0];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function checkPass($data = array()){
		if(($data['username'] || $data['email']) && $data['password']){
			$this->db->select('username, email, password');
			$this->db->from('users');
			if($data['username']) $this->db->where('users.username', $data['username']);
			if($data['email']) $this->db->where('users.email', $data['email']);
			$q = $this->db->get();
			if($q->num_rows() > 0){
				return password_verify($data['password'], $q->result_array()[0]['password']);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
