<?php

class CategoriesModel extends CI_Model{
	/**
	 * @var array
	 */
	private $categories;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbhelp');
		//$this->getCategories(); niepotrzebne
	}

	function getCategories(){ //niepotrzebne
		$this->db->select('*');
		$this->db->from('categories');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$this->categories = $query->result_array();
			return $this;
		}else{
			return FALSE;
		}
	}

	function categoryFind(array $dataFind){
		if(!is_array($dataFind)){
			return FALSE;
		}
		$this->db->select("categories.id_category, categories.category_name, categories.category_parent, categories.enable, (SELECT GROUP_CONCAT(parent.category_name ORDER BY parent.lft SEPARATOR ', ') as path FROM categories AS node, categories AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.id_category = categories.id_category ORDER BY parent.lft) as category_path");
		$this->db->from('categories');
		issetWhere($this->db, $dataFind, 'categories.id_category');
		issetWhere($this->db, $dataFind, 'categories.category_name');
		issetWhere($this->db, $dataFind, 'categories.category_parent');
		issetWhere($this->db, $dataFind, 'categories.enable');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}
	}

	function categoryToRoot($id_category){ //Niepotrzebne
		$needle = $id_category;
		$root = [];
		do {
			$temp_array = [];
			$key = array_search($needle, array_column($this->categories, 'id_category'));
			$temp_array = $this->categories[$key];
			array_push($root, $temp_array);
			$needle = $temp_array['category_parent'];
		}while($needle != 0);
		//var_dump($root);
		asort($root);
		return $root;
	}
}
