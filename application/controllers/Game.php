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
            echo $this->GameModel->create($name, $password);
        }
        else
        {
            echo "404 error";
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
        return $this->GameModel->getGameKey($name, $password);
    }

    // 下載 EzWebGameLib
    public function loadEzWebGameLib($gameId, $gKey)
    {
        $this->load->model("UserModel");
        $auth = $this->GameModel->checkAuth($gameId, $gKey);
        print_r($auth);
        if ($auth != false)
        {
            $key = $this->UserModel->keygen(-1, $gameId, -1);
            $this->GameModel->saveKey($key);
            echo $key;
        }
        else
        {
            echo "404 Error";
        }
    }
}

?>