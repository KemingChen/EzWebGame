<?php

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
    }

    // 創建使用者帳號密碼
    public function signup($name, $account, $password)
    {
        if (!$this->UserModel->exist("account", $account) && !$this->UserModel->exist("userName", $name))
        {
            echo $this->UserModel->create($name, $account, $password);
        }
        else
        {
            echo "404 error";
        }
    }

    // 確認名字是否存在
    public function isNameExist($name)
    {
        echo $this->UserModel->exist("userName", $name);
    }

    // 確認此帳號是否存在
    public function isAccountExist($account)
    {
        echo $this->UserModel->exist("account", $account);
    }

    // 登入
    public function login($lKey, $gameId, $account, $password)
    {
        $this->load->model("GAuthModel");
        $gAuth = $this->GAuthModel->checkLoginKey($gameId, $lKey); // 確認此key可以用來登入此遊戲
        $auth = $this->UserModel->checkAuth($account, $password);
        //print_r($auth);
        if ($gAuth && $auth != false)
        {
            $this->load->model("AuthModel");
            $this->GAuthModel->deleteLoginKey($lKey); // 刪除登入時使用的Key
            $lKey = $this->AuthModel->commuKeygen($auth["id"], $gameId, -1); // 產生新的溝通key
            $this->AuthModel->saveCommuKey($auth["id"], $gameId, $lKey); // 儲存溝通key 使下次可以做認證
            echo $lKey;
        }
        else
        {
            echo 0;
        }
    }

    // 登出
    public function logout($ckey)
    {
        $this->load->model("AuthModel");
        $this->AuthModel->deleteCommuKey($ckey);
    }
}

?>