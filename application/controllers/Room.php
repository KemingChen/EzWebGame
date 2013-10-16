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

    // 創建房間
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);

        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $roomId = $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer);
        $this->out->save("Create", $roomId);
        $this->out->show();
    }

    // 加入房間
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

    // 離開房間
    public function leave($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->leave($userId, $roomId, $this->out);
        $this->AuthModel->editCommuKey($nextCKey, 0, $this->out);
        $this->out->save("Leave", true);
        $this->out->show();
    }

    // 查詢所有未開始房間
    public function ListRoomInfos($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->roomInfo($gameId, $this->out);
        $this->out->show();
    }

    // 查詢某房間的所有玩家
    public function ListRoomPlayers($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->playerInfo($roomId, $this->out);
        $this->out->show();
    }

    // 修改房間名稱
    public function modifyTitle($iTitle, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $data = array("title" => $iTitle);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    // 修改房間 玩家人數上下限
    public function ModifyMinMaxPlayer($minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        checkPlayerNumber($minPlayer, $maxPlayer);

        $data = array("min" => $minPlayer, "max" => $maxPlayer);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    // 遊戲設定玩家人數上下限 防呆機制
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 2 && $minPlayer <= $maxPlayer && $maxPlayer <= 20))
        {
            $this->out->wrong("MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer");
        }
    }
}

?>