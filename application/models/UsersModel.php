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
    }



}
