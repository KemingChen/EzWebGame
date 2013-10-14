<?php

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
    }

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('header');
    }

    //創建使用者帳號密碼
    public function create($name, $account, $password)
    {
        echo $this->UserModel->create($name, $account, $password);
    }

    //確認名字是否存在
    public function isNameExist($name)
    {
        echo $this->UserModel->exist("userName", $name);
    }

    //確認此帳號是否存在
    public function isAccountExist($name)
    {
        echo $this->UserModel->exist("account", $name);
    }

    //登入
    public function login($game, $account, $password)
    {
        $auth = $this->UserModel->checkLogin($account, $password);
        //print_r($auth);
        if ($auth != false)
        {
            $key = $this->UserModel->keygen($auth["id"], $game, -1);
            echo $key;
        }
        else
        {
            echo 0;
        }
    }
    
    public function logout($key)
    {
        $this->UserModel->deleteKey($key);
    }
}

?>