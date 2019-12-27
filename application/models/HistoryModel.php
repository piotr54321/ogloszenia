<?php

class HistoryModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function logowanie(array $dataFind = []){
		$this->db->select('*');
		$this->db->from('login_history');
		issetWhere($this->db, $dataFind, 'id_user');
		issetWhere($this->db, $dataFind, 'address_ip');
		issetWhere($this->db, $dataFind, 'device');
		issetWhere($this->db, $dataFind, 'login_time');
		$query = $this->db->get();

		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
}
