<?php

class AdsModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbHelp');
	}

	function adInsert(array $dataInsert){
		if(!is_array($dataInsert)){
			return FALSE;
		}
		//exit;
		issetSet($this->db, $dataInsert, 'id_category');
		issetSet($this->db, $dataInsert, 'id_user');
		issetSet($this->db, $dataInsert, 'offer_name');
		issetSet($this->db, $dataInsert, 'accept');
		issetSet($this->db, $dataInsert, 'description');
		issetSet($this->db, $dataInsert, 'create_time');
		issetSet($this->db, $dataInsert, 'end_time');
		issetSet($this->db, $dataInsert, 'id_address');
		issetSet($this->db, $dataInsert, 'negotiation');
		issetSet($this->db, $dataInsert, 'price');
		issetSet($this->db, $dataInsert, 'id_currency');
		$this->db->insert('offers');
		if($this->db->affected_rows() > 0){
			$dataInsert['insert_id'] = $this->db->insert_id();
			return $dataInsert;
		}else{
			return FALSE;
		}
	}

	function imageInsert(array $dataImage){
		if(!is_array($dataImage)){
			return FALSE;
		}
		issetSet($this->db, $dataImage, 'id_offer');
		issetSet($this->db, $dataImage, 'filename');
		issetSet($this->db, $dataImage, 'id_user');
		$this->db->insert('images');

		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return FALSE;
		}
	}
}
