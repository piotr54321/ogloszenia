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

	function checkPass($data = array())
	{
		$this->db->select('username, password');
		$this->db->from('users');
		$this->db->where('username', $data['username']);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return password_verify($data['password'], $q->result_array()[0]['password']);
		} else {
			return false;
		}
	}

	function sessionUserData($username)
	{
		$this->db->select('id as user_id, username, email');
		$this->db->from('users');
		$this->db->where('username', $username);
		$this->db->limit(1);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			return $q->result_array()[0];
		} else {
			return false;
		}
	}

	function addLoginHistory(int $userID)
    {
		$ip = $_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : ($_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

    	$this->db->set('id_user', $userID);
    	$this->db->set('address_ip', $ip);
    	$this->db->set('device', $_SERVER['HTTP_USER_AGENT']);
    	$this->db->insert('login_history');
    	if($this->db->affected_rows() > 0){
    		return true;
		}else{
    		return false;
		}
    }
}
