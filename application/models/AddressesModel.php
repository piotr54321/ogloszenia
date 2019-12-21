<?php

class AddressesModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbHelp');
	}

	function adressesFind(array $dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select('id_address, id_user, main_address, postal_code, home, street, city, name');
		$this->db->from('addresses');
		issetWhere($this->db, $dataFind, 'id_address');
		issetWhere($this->db, $dataFind, 'id_user');
		issetWhere($this->db, $dataFind, 'main_address');
		issetWhere($this->db, $dataFind, 'postal_code');
		issetWhere($this->db, $dataFind, 'home');
		issetWhere($this->db, $dataFind, 'street');
		issetWhere($this->db, $dataFind, 'city');
		issetWhere($this->db, $dataFind, 'name');
		$query = $this->db->get();

		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function addressUpdate(array  $dataUpdate){

		$this->db->trans_start();
		if($dataUpdate['update']['main_address'] == 1){
			$this->addressUpdate(['update' => ['main_address' => 0], 'where' => ['id_user' => $dataUpdate['where']['id_user']]]);
		}
		issetSet($this->db, $dataUpdate['update'], 'name');
		issetSet($this->db, $dataUpdate['update'], 'city');
		issetSet($this->db, $dataUpdate['update'], 'street');
		issetSet($this->db, $dataUpdate['update'], 'home');
		issetSet($this->db, $dataUpdate['update'], 'postal_code');
		issetSet($this->db, $dataUpdate['update'], 'main_address');
		issetWhere($this->db, $dataUpdate['where'], 'id_address');
		issetWhere($this->db, $dataUpdate['where'], 'id_user');
		$this->db->update('addresses');

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function addressInsert(array $dataInsert){
		$this->db->trans_start();
		if($dataInsert['insert']['main_address'] == 1){
			$this->addressUpdate(['update' => ['main_address' => 0], 'where' => ['id_user' => $dataInsert['insert']['id_user']]]);
		}
		issetSet($this->db, $dataInsert['insert'], 'id_user');
		issetSet($this->db, $dataInsert['insert'], 'city');
		issetSet($this->db, $dataInsert['insert'], 'name');
		issetSet($this->db, $dataInsert['insert'], 'street');
		issetSet($this->db, $dataInsert['insert'], 'home');
		issetSet($this->db, $dataInsert['insert'], 'postal_code');
		issetSet($this->db, $dataInsert['insert'], 'main_address');
		$this->db->insert('addresses');
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE){
			return $insert_id;
		}else{
			return FALSE;
		}
	}

	function addressDelete(array $dataDelete){
		issetWhere($this->db, $dataDelete['delete'], 'id_address');
		issetWhere($this->db, $dataDelete['where'], 'id_user');
		$this->db->delete('addresses');

		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}
