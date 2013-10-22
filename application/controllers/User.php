<?php

class User extends CI_Controller
{
    /**
     * User::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("UserModel");
    }

    /**
     * User::signup()
     * 
     * 創建使用者帳號密碼
     * 
     * @param mixed $name
     * @param mixed $account
     * @param mixed $password
     * @return void
     */
    public function signup($name, $account, $password)
    {
        if (!$this->UserModel->exist("account", $account) && !$this->UserModel->exist("userName",
            $name))
        {
            $userId = $this->UserModel->create($name, $account, $password);
            $this->out->save("userId", $userId);
        }
        else
        {
            $this->out->wrong("Account or UserName Repeat");
        }
        $this->out->show();
    }

    /**
     * User::isNameExist()
     * 
     * 確認名字是否存在
     * 
     * @param mixed $name
     * @return void
     */
    public function isNameExist($name)
    {
        $isExist = $this->UserModel->exist("userName", $name);
        $this->out->save("NameExist", $isExist);
        $this->out->show();
    }

    /**
     * User::isAccountExist()
     * 
     * 確認此帳號是否存在
     * 
     * @param mixed $account
     * @return void
     */
    public function isAccountExist($account)
    {
        $isExist = $this->UserModel->exist("account", $account);
        $this->out->save("AccountExist", $isExist);
        $this->out->show();
    }

    /**
     * User::login()
     * 
     * 登入
     * 
     * @param mixed $lKey
     * @param mixed $account
     * @param mixed $password
     * @return void
     */
    public function login($lKey, $account, $password)
    {
        $this->load->model("GAuthModel");
        $gAuth = $this->GAuthModel->checkLoginKey($lKey); // 確認此key可以用來登入此遊戲
        $auth = $this->UserModel->checkAuth($account, $password);

        if ($gAuth && $auth != false)
        {
            $this->load->model("AuthModel");
            $this->GAuthModel->deleteLoginKey($lKey); // 刪除登入時使用的Key
            list($key, $gameId) = explode('_', $lKey);
            $nextCKey = $this->AuthModel->commuKeygen($auth["id"], $gameId, 0); // 產生 溝通key
            $this->AuthModel->saveCommuKey($auth["id"], $gameId, $nextCKey); // 儲存溝通key
            $this->out->save("cKey", $nextCKey);
            
            // 刪除之前所在之房間
            $this->load->model("RoomModel", "room");
            $this->room->deleteSelfFromAnyRoom($auth["id"]);
        }
        else
        {
            $this->out->wrong("Authentication failed");
        }
        $this->out->show();
    }
    
    /**
     * User::cancelLogin()
     * 
     * 取消此 登入Key
     * 
     * @param mixed $lKey
     * @return void
     */
    public function cancelLogin($lKey)
    {
        $this->load->model("GAuthModel");
        $this->GAuthModel->deleteLoginKey($lKey);
    }
    
    /**
     * User::logout()
     * 
     * 登出
     * 
     * @param mixed $ckey
     * @return void
     */
    public function logout($ckey)
    {
        $this->load->model("AuthModel");
        $this->AuthModel->deleteCommuKey($ckey);
        $this->out->save("Logout", true);
        $this->out->show();
    }
}

?>