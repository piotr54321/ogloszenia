<?php

class WalletModel extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbhelp');
	}

	function walletsFind(array $dataFind)
	{
		$this->db->select('wallet.id_wallet, wallet.id_user, wallet.id_currency, wallet.amount, currencies.id_currency, currencies.full_name, currencies.currency_code, currencies.enabled');
		$this->db->from('wallet');
		$this->db->join('currencies', 'currencies.id_currency = wallet.id_currency');
		issetWhere($this->db, $dataFind, 'wallet.id_wallet');
		issetWhere($this->db, $dataFind, 'wallet.id_user');
		issetWhere($this->db, $dataFind, 'wallet.id_currency');
		issetWhere($this->db, $dataFind, 'wallet.amount', '>=');
		issetWhere($this->db, $dataFind, 'currencies.id_currency');
		issetWhere($this->db, $dataFind, 'currencies.full_name');
		issetWhere($this->db, $dataFind, 'currencies.currency_code');
		issetWhere($this->db, $dataFind, 'currencies.enabled');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	function currenciesFind(array $dataFind)
	{
		$this->db->select('id_currency, full_name, currency_code, enabled');
		$this->db->from('currencies');
		issetWhere($this->db, $dataFind, 'id_currency');
		issetWhere($this->db, $dataFind, 'full_name');
		issetWhere($this->db, $dataFind, 'currency_code');
		issetWhere($this->db, $dataFind, 'enabled');
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return FALSE;
		}
	}

	function walletCreate(array $dataInsert)
	{
		issetSet($this->db, $dataInsert, 'id_user');
		issetSet($this->db, $dataInsert, 'id_currency');
		issetSet($this->db, $dataInsert, 'amount');
		$this->db->insert('wallet');

		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * @param array $dataUpdate
	 * @return bool
	 */
	function walletUpdate(array $dataUpdate)
	{
		//Sprawdzenie czy wszystkie parametry zostały poprawnie podane
		if (!is_numeric($dataUpdate['id_currency']) || !is_numeric($dataUpdate['id_user'])
			|| !is_bool($dataUpdate['operation']) || !is_numeric($dataUpdate['amount']) || $dataUpdate['amount'] <= 0) {
			return FALSE;
		}

		$this->db->trans_start();
		if (!is_array($this->walletsFind([
			'wallet.id_user' => $dataUpdate['id_user'], 'wallet.id_currency' => $dataUpdate['id_currency']
		]))) {
			if (!$this->walletCreate([
				'id_user' => $dataUpdate['id_user'], 'id_currency' => $dataUpdate['id_currency']
			])) {
				return FALSE;
			}
		}

		$operator = (($dataUpdate['operation'] == true) ? '+' : '-');
		$this->db->set('amount', "amount" . $operator . $dataUpdate['amount'], false);
		issetWhere($this->db, $dataUpdate, 'id_user');
		issetWhere($this->db, $dataUpdate, 'id_currency');
		$this->db->update('wallet');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
