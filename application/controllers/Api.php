<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 31.03.2019
 * Time: 20:39
 */

use Firebase\JWT\JWT;

class Api extends \Restserver\Libraries\REST_Controller
{
    function __construct()
    {
        parent::__construct();
        //->authValidate();
        $this->load->library('aclsystem');
    }

    function index_get(){

    }

    function user_put(){

    }

    function user_post(){

    }

    function user_get(){

    }

    function guest_get(){
        $this->aclsystem->getUserId(1);
        $rangi = $this->aclsystem->getAllUserRanksList();
        $uprawnienia = array_column($rangi, 'rank');
        $dostepneStrony = array_values($this->aclsystem->getAllUserResources($uprawnienia));
        $this->response([
            'message' => 'Tryb gościa',
            'page_data' => $dostepneStrony
        ], 200);
    }

    function auth_post(){

        $data = [
            'username' => $this->post('username'),
            'email' => $this->post('email'),
            'password' => $this->post('password')
        ];

        $checkUser = $this->usersmodel->user($data);
        $checkPass = $this->usersmodel->checkPass($data);


        if($checkUser && $checkPass){
            $iss = "http://localhost"; // Wydawca
            $iat = time(); // Czas rejestracji tokena
            $exp = time() + 10*60; // 10 minut ważny token
            $this->aclsystem->getUserId($checkUser['id']);
            $rangi = $this->aclsystem->getAllUserRanksList();
            $uprawnienia = array_column($rangi, 'rank');
            $dostepneStrony = json_encode(array_values($this->aclsystem->getAllUserResources($uprawnienia)));

            $token = [
                'iss' => $iss,
                'exp' => $exp,
                'iat' => $iat,
                'data' => [
                    'id' => $checkUser['id'],
                    'username' => $checkUser['username'],
                    'email' => $checkUser['email']
                ]
            ];

            $jwtToken = JWT::encode($token, $this->config->item('jwt_key'));
            $this->response([
                'message' => 'Zalogowano',
                'jwt_token' => $jwtToken,
                'page_data' => $dostepneStrony
            ], 200);
        }else{
            $this->response([
                'message' => 'Nie zalogowano'
            ], 401);
        }
    }
}
