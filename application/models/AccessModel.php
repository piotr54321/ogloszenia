<?php

class AccessModel extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbHelp');
	}

	function getPageResources(array $find = null)
	{
		$this->db->select('id, link_name, name, rank_min, login_require');
		$this->db->from('page_resources');
		issetWhere($this->db, $find, 'id');
		issetWhere($this->db, $find, 'link_name');
		issetWhere($this->db, $find, 'name');
		issetWhere($this->db, $find, 'rank_min');
		issetWhere($this->db, $find, 'login_require');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function getPageRoles(array $find = null)
	{
		$this->db->select('id, rank, rolename');
		$this->db->from('page_roles');
		issetWhere($this->db, $find, 'id');
		issetWhere($this->db, $find, 'rank');
		issetWhere($this->db, $find, 'rolename');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}
}
