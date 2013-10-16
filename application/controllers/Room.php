<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("RoomModel");
        $this->load->model("AuthModel");
    }

    // �Ыةж�
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey);
        $this->out->save("cKey", $nextCKey);

        if ($this->checkPlayerNumber($minPlayer, $maxPlayer))
        {
            list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
            $roomId = $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer);
            $this->out->save("roomId", $roomId);
        }

        $this->out->show();
    }

    // �C���]�w���a�H�ƤW�U�� ���b����
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 2 && $minPlayer <= $maxPlayer && $maxPlayer <= 20))
        {
            $this->out->error("MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer");
            $this->out->error("vremgkrwmkgl");
            $this->out->error("qklnqjkewnfklewfgl");
            return false;
        }
        return true;
    }
}

?>