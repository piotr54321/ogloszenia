<?php

class HistoryModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function loginHistory(array $dataFind = []){
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

	function walletHistoryAdd(array $dataInsert){
		$this->db->set('id_user', $dataInsert['id_user']);
		$this->db->set('id_wallet', $dataInsert['id_wallet']);
		$this->db->set('type', $dataInsert['type']);
		$this->db->set('amount', $dataInsert['amount']);
		$this->db->insert('wallet_history');

		if($this->db->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}

	function walletHistoryFind(array $dataFind = []){
		$this->db->select('wallet_history.id_user, wallet_history.id_wallet, wallet_history.type, wallet_history.amount, wallet_history.wallet_amount, currencies.currency_code');
		$this->db->from('wallet_history');
		$this->db->join('wallet', 'wallet_history.id_wallet = wallet.id_wallet');
		$this->db->join('currencies', 'currencies.id_currency = wallet.id_currency');
		issetWhere($this->db, $dataFind, 'wallet_history.id');
		issetWhere($this->db, $dataFind, 'wallet_history.id_user');
		issetWhere($this->db, $dataFind, 'wallet_history.id_wallet');
		issetWhere($this->db, $dataFind, 'wallet_history.type');
		issetWhere($this->db, $dataFind, 'wallet_history.amount');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
}
