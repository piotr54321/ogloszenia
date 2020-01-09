<?php

class ChatModel extends CI_Model
{
	/**
	 * @var array
	 */
	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbhelp');
	}

	function getMesseges($dataFind)
	{
		$this->db->select('msgs.id, msgs.id_offer, msgs.id_user, msgs.text, msgs.time, msgs.reply');
		$this->db->from('msgs');
		$this->db->join('offers', 'msgs.id_offer = offers.id_offer');
		issetWhere($this->db, $dataFind['where'], 'msgs.id');
		issetWhere($this->db, $dataFind['where'], 'msgs.text');
		issetWhere($this->db, $dataFind['where'], 'msgs.time');
		$this->db->where('msgs.id_user', $dataFind['where']['msgs.id_user']);
		$this->db->where('msgs.id_offer', $dataFind['where']['msgs.id_offer']);
		//issetWhere($this->db, $dataFind['where'], 'msgs.id_offer');
		//issetWhere($this->db, $dataFind['where'], 'msgs.id_user');
		//$this->db->or_where('offers.id_user', $dataFind['where']['offers.id_user']);
		//issetWhere($this->db, $dataFind['where'], 'offers.id_user');
		$this->db->order_by('id', 'ASC');
		if (isset($dataFind['limit'])) {
			$this->db->limit($dataFind['limit']);
		}

		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getChats($dataFind){
		$this->db->select('msgs.id_user as user_from, users.username, offers.offer_name, offers.id_user as user_to, msgs.id_offer');
		$this->db->distinct();
		$this->db->from('msgs');
		$this->db->join('offers', 'offers.id_offer = msgs.id_offer');
		$this->db->join('users', 'users.id = msgs.id_user');
		issetWhere($this->db, $dataFind['where'], 'offers.id_user');
		$this->db->or_where('msgs.id_user', $dataFind['where']['offers.id_user']);

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}

	function saveMessage($data){
		$this->db->set('id_offer', $data['id_offer']);
		$this->db->set('id_user', $data['id_user']);
		$this->db->set('text', $data['text']);
		$this->db->set('reply', $data['reply']);
		$this->db->insert('msgs');
		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
}
