<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("RoomModel");
        $this->load->model("AuthModel");
    }

    // �Ыةж�
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nCKey = $this->AuthModel->getNextCommuKey($cKey);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);
        list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
        echo json_encode(array("roomId" => $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer), "cKey" => $nCKey));
    }

    // �C���]�w���a�H�ƤW�U�� ���b����
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