<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 31.03.2019
 * Time: 20:39
 */

use Firebase\JWT\JWT;

header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");

class ApiAuth extends \Restserver\Libraries\REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->authValidate();
    }

    function index_get(){

    }

    function getTokenFromHeader(){
        return substr($this->input->server('HTTP_AUTHORIZATION'), 7);
    }

    function authValidate(){
        $dataToken = $this->getTokenFromHeader();
        //var_dump($dataToken);
        if(isset($dataToken->jwt_token)){
            try{
                JWT::$leeway = 5;
                $jwtDecode = JWT::decode($dataToken->jwt_token, $this->config->item('jwt_key'), ['HS256']);

                $this->response([
                    'message' => 'Ok',
                    'data' => $jwtDecode->data
                ], 200);
            }catch (Exception $exception){
                $this->response([
                    'message' => 'Deny',
                    'error' => $exception->getMessage()
                ], 401);
            }
        }else{
            $this->response([
                'message' => 'Deny',
                'error' => 'Unauthorized'
            ], 401);
        }
    }
}
