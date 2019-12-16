<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 30.01.2019
 * Time: 21:10
 */

class ACLsystem{
    protected $CI;
    protected $user_id=0;
    public $allResourcesNames;
    public $getAllUserRanksList;
    public $getAllPageResources;
    public $getUserResourceAccess;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
        $this->getUserId();
    }

    public function getUserId($USER_ID=1){
        if($this->CI->session->user_id){
            $USER_ID = $this->CI->session->user_id;
        }
        $this->user_id = $USER_ID;
        //var_dump($this->user_id);
        return $this;
    }

    public function getAllUserRanksList(){
        $this->CI->db->select('rank, rolename');
        $this->CI->db->from('page_roles');
        $this->CI->db->join('users_roles', 'users_roles.page_roles_id=page_roles.id');
        $this->CI->db->where('users_id', $this->user_id);
        $q = $this->CI->db->get();
        if($q->num_rows() > 0){
            $this->getAllUserRanksList = $q->result_array();
            return $this->getAllUserRanksList;
        }else{
            return false;
        }
    }

    public function getAllPageResources(){
        $this->CI->db->select('link_name, rank_min, login_require');
        $this->CI->db->from('page_resources');
        $q = $this->CI->db->get();
        if($q->num_rows() > 0){
            $this->getAllPageResources = $q->result_array();
            return $this->getAllPageResources;
        }else{
            return false;
        }
    }

    public function getResourceAccessForRanksRoles($roles_list=array(), $resource_name = false){
        array_push($roles_list, 0);
        $this->CI->db->select('link_name');
        $this->CI->db->from('page_resources');
        $this->CI->db->where_in('rank_min', $roles_list);
        if($resource_name){
            $this->CI->db->where('link_name', $resource_name);
        }
        $q = $this->CI->db->get();
        if($q->num_rows() > 0){
            return $q->result_array();
        }else{
            return false;
        }
    }

    public function getUserResourceAccess(){
        $this->CI->db->select('resource_id, allow, link_name');
        $this->CI->db->from('users_page_resources');
        $this->CI->db->join('page_resources', 'users_page_resources.resource_id=page_resources.id');
        $this->CI->db->where('users_id', $this->user_id);
        $q = $this->CI->db->get();
        if ($q->num_rows() > 0){
            $this->getUserResourceAccess = $q->result_array();
            return $this->getUserResourceAccess;
        }else{
            return false;
        }
    }

	/**
	 * Zwraca listę wszystkich dostępnych zasobów dla użytkownika
	 * a.) Dostępne zasoby dla grup do których przydzielony jest użytkownik
	 * b.) Zasoby dla których użytkownik dodatkowo poza grupą otrzymał lub odebrano mu dostęp
	 * @param $roles_list
	 * @return array
	 */
	public function getAllUserResources($roles_list){
        $resourcesForRanks = $this->getResourceAccessForRanksRoles($roles_list); // a.)
        $allResourcesNames = array_column($resourcesForRanks, 'link_name');
        $userResourceAccessList = $this->getUserResourceAccess(); // b.)
        if(is_array($userResourceAccessList)){
            $resourceKeysAllow = array_keys(array_column($userResourceAccessList, 'allow'), '1');
            $resourceKeysDisallow = array_keys(array_column($userResourceAccessList, 'allow'), '0');
            if(count($resourceKeysAllow) > 0){
                $array1 = [];
                foreach ($resourceKeysAllow as $key){
                    array_push($array1, $userResourceAccessList[$key]);
                }
                $allResourcesNames = array_merge($allResourcesNames, array_column($array1, 'link_name'));
            }
            if(count($resourceKeysDisallow) > 0){
                $array2 = [];
                foreach ($resourceKeysDisallow as $key){
                    array_push($array2, $userResourceAccessList[$key]);
                }
                $allResourcesNames = array_diff($allResourcesNames, array_column($array2, 'link_name'));
            }
        }
        $this->allResourcesNames = array_unique($allResourcesNames);
        return $this->allResourcesNames;
    }

    public function checkResourceAccessV1($resource_name){ // v1.0
        $user_resource_access_list = $this->getUserResourceAccess();
        if(is_array($user_resource_access_list)){
            $resource_key = array_search($resource_name, array_column($user_resource_access_list, 'link_name'));
            if($resource_key !== false){
                $allow = $user_resource_access_list[$resource_key]['allow'];
                //var_dump($allow);
                if($allow == 1) {
                    return true;
                } elseif ($allow == 0) {
                    return false;
                }
            }
        }
        $roles_list = $this->getAllUserRanksList();
        if(!$roles_list){
            return false;
        }
        $roles_list = array_column($roles_list, 'rank');
        //var_dump($roles_list);
        if($this->getResourceAccessForRanksRoles($roles_list, $resource_name)){
            return true;
        }else{
            return false;
        }
    }

	/**
	 * Zwraca prawdę jeżeli użytkownik ma dostęp do określonego zasobu
	 *
	 * @param $resourceName
	 * @return bool
	 */
	public function checkResourceAccess($resourceName){
        if(in_array($resourceName, $this->getAllUserResources(array_column($this->getAllUserRanksList(), 'rank')))){
            return true;
        }else{
            return false;
        }
    }
}
