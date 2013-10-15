<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("RoomModel");
        $this->load->model("AuthModel");
    }

    // 創建房間
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nCKey = $this->AuthModel->getNextCommuKey($cKey);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);
        list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
        echo json_encode(array("roomId" => $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer), "cKey" => $nCKey));
    }

    // 遊戲設定玩家人數上下限 防呆機制
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 2 && $minPlayer <= $maxPlayer && $maxPlayer <= 20))
        {
            echo json_encode(array("Error" => "MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer"));
            exit;
        }
    }
}

?>