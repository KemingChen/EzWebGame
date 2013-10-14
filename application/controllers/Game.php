<?php

class Game extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("GameModel");
    }
    
    public function loadEzWebGameLib($gameId, $gKey)
    {
        $this->load->model("UserModel");
        $auth = $this->GameModel->checkAuth($gameId, $gKey);
        print_r($auth);
        if($auth != false)
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