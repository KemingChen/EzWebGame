<?php

class Game extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("GameModel");
    }

    // 創建遊戲
    public function create($name, $password)
    {
        if (!$this->isNameExist($name))
        {
            $this->load->model("AuthModel");
            $gKey = $this->AuthModel->keygen(18);
            echo 'Your Key is<br>'.$this->GameModel->create($name, $gKey, $password);
        }
        else
        {
            echo "Name has been used!";
        }
    }

    // 此遊戲名稱是否存在
    public function isNameExist($name)
    {
        return $this->GameModel->exist("gameName", $name);
    }
    
    // 得到 gKey
    public function getGameKey($name, $password)
    {
        $gKey = $this->GameModel->getGameKey($name, $password);
        if($gKey=='0')
            echo 'Incorrect Name or Password';
        else
            echo 'Your Key is<br>'.$gKey;
    }

    // 下載 EzWebGameLib
    public function loadEzWebGameLib($gKey)
    {
        $auth = $this->GameModel->checkAuth($gKey);
        //print_r($auth);
        if ($auth != false)
        {
            $this->load->model("AuthModel");
            $this->load->model("GAuthModel");
            $loginKey = sprintf("%s_%d", $this->AuthModel->keygen(12), $auth["id"]);
            $this->GAuthModel->saveLoginKey($loginKey);
            echo $loginKey;
        }
        else
        {
            echo "404 Error";
        }
    }
}

?>