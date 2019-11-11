<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 01.04.2019
 * Time: 10:05
 */

class UsersModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('dbHelp');
	}

	/**
	 * @param array|null $userFind [Tablica z zmiennymi do szukania]
	 * @return array|bool
	 */
	function getUsers(array $userFind = null)
	{
		$this->db->select('id, username, email, banned, ');
		$this->db->from('users');
		issetWhere($this->db, $userFind, 'id');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getUsersRoles(int $userId)
	{
		if (!$userId) {
			return false;
		}
		$this->db->select(
			'users_roles.id as id_users_roles, users_roles.users_id, users_roles.page_roles_id, page_roles.rank, page_roles.rolename'
		);
		$this->db->from('users_roles');
		$this->db->join(
			'page_roles',
			'users_roles.page_roles_id=page_roles.id'
		);
		$this->db->where('users_roles.users_id', $userId);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	/**
	 * Funkcja pokazuje wszystkie dostępne w bazie role i wyszczególnia za pomocą 'kolumny' user_have te które użytkownik posiada
	 * @param int $userId
	 * @return bool
	 */
	function getUsersRoles2(int $userId){
		if (!$userId) {
			return false;
		}
		$query = $this->db->query('SELECT page_roles.id, page_roles.rank, page_roles.rolename, IF(page_roles.id IN(SELECT page_roles_id FROM users_roles WHERE users_roles.users_id = ?), 1, 0) as user_have FROM page_roles', array($userId));
		//$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return FALSE;
		}

	}

	function getUserResources(int $userId)
	{
		if (!$userId) {
			return false;
		}
		$this->db->select(
			'users_page_resources.id as id_users_page_resources, users_page_resources.users_id, users_page_resources.resource_id, users_page_resources.allow, page_resources.id, page_resources.link_name, page_resources.name, page_resources.rank_min, page_resources.login_require'
		);
		$this->db->from('users_page_resources');
		$this->db->join(
			'page_resources',
			'users_page_resources.resource_id=page_resources.id'
		);
		$this->db->where('users_page_resources.users_id', $userId);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getUserResources2(int $userId){
		if (!$userId) {
			return false;
		}
	}

	function userUpdate(array $dataUpdate){
		if(!is_numeric($dataUpdate['id'])){
			return false;
		}

		if(isset($dataUpdate['password'])){
			$this->db->set('password', password_hash($dataUpdate['password'], PASSWORD_DEFAULT));
		}
		issetSet($this->db, $dataUpdate, 'username');
		issetSet($this->db, $dataUpdate, 'email');
		issetSet($this->db, $dataUpdate, 'banned');
		issetWhere($this->db, $dataUpdate, 'id');
		$this->db->update('users');
		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * @param array $dataUpdate
	 * @return bool
	 */
	function userRolesUpdate(array $dataUpdate){
		if(!is_numeric($dataUpdate['id'])){
			return FALSE;
		}
		$this->db->trans_start();
		$this->db->delete('users_roles', ['users_id' => $dataUpdate['id']]);
		foreach ($dataUpdate['roles'] as $role){
			$this->db->set(['page_roles_id' => $role, 'users_id' => $dataUpdate['id']]);
			$this->db->insert('users_roles');
		}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}else{
			return TRUE;
		}
	}
}
