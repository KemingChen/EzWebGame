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
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);

        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $roomId = $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer);
        $this->out->save("Create", $roomId);
        $this->out->show();
    }

    // �[�J�ж�
    public function join($iRoomId, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $roomId = $this->RoomModel->join($userId, $iRoomId, $this->out);
        if ($roomId != false)
        {
            $this->RoomModel->roomInfo($gameId, $this->out, $roomId);
            $this->RoomModel->playerInfo($roomId, $this->out);
        }
        $this->AuthModel->editCommuKey($nextCKey, $iRoomId, $this->out);
        $this->out->save("Join", $roomId);
        $this->out->show();
    }

    // ���}�ж�
    public function leave($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->leave($userId, $roomId, $this->out);
        $this->AuthModel->editCommuKey($nextCKey, 0, $this->out);
        $this->out->save("Leave", true);
        $this->out->show();
    }

    // �d�ߩҦ����}�l�ж�
    public function ListRoomInfos($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->roomInfo($gameId, $this->out);
        $this->out->show();
    }

    // �d�߬Y�ж����Ҧ����a
    public function ListRoomPlayers($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->playerInfo($roomId, $this->out);
        $this->out->show();
    }

    // �ק�ж��W��
    public function modifyTitle($iTitle, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $data = array("title" => $iTitle);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    // �ק�ж� ���a�H�ƤW�U��
    public function ModifyMinMaxPlayer($minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        checkPlayerNumber($minPlayer, $maxPlayer);

        $data = array("min" => $minPlayer, "max" => $maxPlayer);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    // �C���]�w���a�H�ƤW�U�� ���b����
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 2 && $minPlayer <= $maxPlayer && $maxPlayer <= 20))
        {
            $this->out->wrong("MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer");
        }
    }
}

?>