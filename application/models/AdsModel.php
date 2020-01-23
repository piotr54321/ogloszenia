<?php

class AdsModel extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbHelp');
		$this->load->helper('filehelp');
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

	function adUpdate(array $dataUpdate){
		if(!is_array($dataUpdate)){
			return FALSE;
		}elseif (!is_array($dataUpdate['where'])){
			return FALSE;
		}elseif (!is_array($dataUpdate['update'])){
			return FALSE;
		}
		//SET
		issetSet($this->db, $dataUpdate['update'], 'id_category');
		issetSet($this->db, $dataUpdate['update'], 'id_user');
		issetSet($this->db, $dataUpdate['update'], 'offer_name');
		issetSet($this->db, $dataUpdate['update'], 'accept');
		issetSet($this->db, $dataUpdate['update'], 'description');
		issetSet($this->db, $dataUpdate['update'], 'create_time');
		issetSet($this->db, $dataUpdate['update'], 'end_time');
		issetSet($this->db, $dataUpdate['update'], 'id_address');
		issetSet($this->db, $dataUpdate['update'], 'negotiation');
		issetSet($this->db, $dataUpdate['update'], 'price');
		issetSet($this->db, $dataUpdate['update'], 'id_currency');
		issetSet($this->db, $dataUpdate['update'], 'ended');

		//WHERE
		issetWhere($this->db, $dataUpdate['where'], 'id_offer');
		issetWhere($this->db, $dataUpdate['where'], 'id_category');
		issetWhere($this->db, $dataUpdate['where'], 'id_offer');
		issetWhere($this->db, $dataUpdate['where'], 'id_user');
		issetWhere($this->db, $dataUpdate['where'], 'offer_name');
		issetWhere($this->db, $dataUpdate['where'], 'accept');
		issetWhere($this->db, $dataUpdate['where'], 'description');
		issetWhere($this->db, $dataUpdate['where'], 'create_time');
		issetWhere($this->db, $dataUpdate['where'], 'end_time');
		issetWhere($this->db, $dataUpdate['where'], 'id_address');
		issetWhere($this->db, $dataUpdate['where'], 'negotiation');
		issetWhere($this->db, $dataUpdate['where'], 'price');
		issetWhere($this->db, $dataUpdate['where'], 'id_currency');
		issetWhere($this->db, $dataUpdate['where'], 'ended');

		$this->db->update('offers');
		if($this->db->affected_rows() > 0){
			return TRUE;
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

	function adsFind(array $dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select('offers.id_offer, offers.id_category, offers.id_user, offers.offer_name, offers.accept, offers.description, offers.create_time, offers.end_time, offers.id_address, offers.negotiation, offers.price, offers.id_currency, (SELECT filename FROM images WHERE id_offer = offers.id_offer LIMIT 1) as main_image');
		$this->db->from('offers');
		//$this->db->join('addresses', 'addresses.id_address = offers.id_address');
		//$this->db->join('currencies', 'currencies.id_currency = offers.id_currency');
		//$this->db->join('users', 'users.id = offers.id_user');

		issetWhere($this->db, $dataFind, 'id_offer');
		issetWhere($this->db, $dataFind, 'id_category');
		issetWhere($this->db, $dataFind, 'id_user');
		issetWhere($this->db, $dataFind, 'offer_name');
		issetWhere($this->db, $dataFind, 'accept');
		issetWhere($this->db, $dataFind, 'description');
		issetWhere($this->db, $dataFind, 'create_time');
		issetWhere($this->db, $dataFind, 'end_time');
		issetWhere($this->db, $dataFind, 'id_address');
		issetWhere($this->db, $dataFind, 'negotiation');
		issetWhere($this->db, $dataFind, 'price');
		issetWhere($this->db, $dataFind, 'id_currency');
		issetWhere($this->db, $dataFind, 'ended');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function imagesFind(array $dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select('*');
		$this->db->from('images');
		issetWhere($this->db, $dataFind, 'id_image');
		issetWhere($this->db, $dataFind, 'id_offer');
		issetWhere($this->db, $dataFind, 'filename');
		issetWhere($this->db, $dataFind, 'id_user');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function observedFind(array $dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select('*');
		$this->db->from('observed');
		issetWhere($this->db, $dataFind, 'id_observation');
		issetWhere($this->db, $dataFind, 'id_offer');
		issetWhere($this->db, $dataFind, 'id_user');
		issetWhere($this->db, $dataFind, 'observation_time');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}


	function observedUpdate(array $dataUpdate){
		if(!is_array($dataUpdate)){
			return -1;
		}

		if($this->observedFind($dataUpdate['where'])){
			// Jeżeli istnieje w obserwowanych -> kasuj obserwację
			issetWhere($this->db, $dataUpdate['update'], 'id_observation');
			issetWhere($this->db, $dataUpdate['update'], 'id_offer');
			issetWhere($this->db, $dataUpdate['update'], 'id_user');
			issetWhere($this->db, $dataUpdate['update'], 'observation_time');
			$this->db->delete('observed');
			if($this->db->affected_rows() > 0){
				return FALSE;
			}
		}else{
			// Jeżeli nie istnieje w obserwowanych -> dodaj obserwację
			issetSet($this->db, $dataUpdate['update'], 'id_observation');
			issetSet($this->db, $dataUpdate['update'], 'id_offer');
			issetSet($this->db, $dataUpdate['update'], 'id_user');
			issetSet($this->db, $dataUpdate['update'], 'observation_time');
			$this->db->insert('observed');
			if($this->db->affected_rows() > 0){
				return TRUE;
			}
		}
		return -1;
	}

	function viewsFind($dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select('*');
		issetWhere($this->db, $dataFind, 'id');
		issetWhere($this->db, $dataFind, 'id_offer');
		issetWhere($this->db, $dataFind, 'date');
		issetWhere($this->db, $dataFind, 'counter');
		$this->db->from('visited');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function addView(int $adId){
		if(!$adId){
			return FALSE;
		}

		$data = date("Y-m-d");
		if(!$this->viewsFind(['id_offer' => $adId, 'date' => $data])){
			$this->db->set('id_offer', $adId);
			$this->db->set('counter', 1);
			$this->db->set('date', $data);
			$this->db->insert('visited');
		}else{
			$this->db->set('counter', 'counter+1', FALSE);
			$this->db->where('date', $data);
			$this->db->where('id_offer', $adId);
			$this->db->update('visited');
		}

		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function countResponses(int $adId) : int{
		$this->db->select('*');
		$this->db->from('msgs');
		$this->db->where('id_offer', $adId);
		$this->db->group_by('id_user');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			return count($query->result_array());
		}else{
			return 0;
		}
	}

	/**
	 * @param $adId
	 * @return array|bool
	 * Odczyt danych licznika odwiedzin dla danego ID ogłoszenia
	 */
	function chartData(int $adId){
		$this->db->select('date as x, counter as y');
		$this->db->from('visited');
		$this->db->where('id_offer', $adId);
		$this->db->order_by('date', 'DESC');
		$this->db->limit(14);

		$query = $this->db->get();
		if($query->num_rows() > 0){
			$result = $query->result_array();
			/*
			$dateArray = $finalArray = $outputData = [];
			$m = date("m");
			$de= date("d");
			$y= date("Y");
			for($i=0; $i<=13; $i++){
				$dateArray[] = ['x' => date('Y-m-d', mktime(0,0,0,$m,($de-$i),$y)), 'y' => 0];
			}
			$dates = array_merge($result, $dateArray);
			$keys = array_column($dates, 'x');
			array_multisort($keys, SORT_DESC, $dates);
			foreach($dates as $item => $item_value) {
				$pid = $item_value['x'];
				if(!isset($finalArray[$pid])) {
					$finalArray[$pid] = $item_value;
				} else {
					$finalArray[$pid]['y'] += $item_value['y'];
				}
			}
			$finalArray = array_slice($finalArray, 0, 14);
			$keys = array_column($finalArray, 'x');
			array_multisort($keys, SORT_ASC, $finalArray);
			$outputData['x'] = array_column($finalArray, 'x');
			$outputData['y'] = array_column($finalArray, 'y');*/// <- wypełnienie zerami tablicy $result w miejsach w których nie było ciągłości dla dat
			return $outputData;
		}else{
			return false;
		}
	}
}
