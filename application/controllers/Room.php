<?php

class Room extends CI_Controller
{
    /**
     * Room::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("RoomModel");
        $this->load->model("AuthModel");
    }

    /**
     * Room::create()
     * 
     * 創建房間
     * 
     * @param mixed $title
     * @param mixed $minPlayer
     * @param mixed $maxPlayer
     * @param mixed $cKey
     * @return void
     */
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);

        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->checkUserNotInAnyRoom($userId, $this->out); //檢查玩家是否有在任何房間中

        $roomId = $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer);
        $this->out->save("Create", $roomId);

        $this->join($roomId, $nextCKey); //自動加入剛創建的房間
    }

    /**
     * Room::join()
     * 
     * 加入房間
     * 
     * @param mixed $iRoomId
     * @param mixed $cKey
     * @return void
     */
    public function join($iRoomId, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $roomId = $this->RoomModel->join($userId, $iRoomId, $this->out);
        if ($roomId != false)
        {
            $this->RoomModel->roomInfo($this->out, $roomId);
            $this->RoomModel->playerInfo($roomId, $this->out);
        }
        $this->AuthModel->editCommuKey($nextCKey, $iRoomId, $this->out);
        $this->out->save("Join", $roomId);
        $this->out->show();
    }

    /**
     * Room::leave()
     * 
     * 離開房間
     * 
     * @param mixed $cKey
     * @return void
     */
    public function leave($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->leave($userId, $roomId, $this->out);
        $this->AuthModel->editCommuKey($nextCKey, 0, $this->out);
        $this->out->save("Leave", true);
        $this->out->show();
    }

    /**
     * Room::ListRoomInfos()
     * 
     * 查詢所有未開始房間
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ListRoomInfos($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->roomInfo($this->out);
        $this->out->show();
    }

    /**
     * Room::ListRoomPlayers()
     * 
     * 查詢某房間的所有玩家
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ListRoomPlayers($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->playerInfo($roomId, $this->out);
        $this->out->show();
    }

    /**
     * Room::modifyTitle()
     * 
     * 修改房間名稱
     * 
     * @param mixed $iTitle
     * @param mixed $cKey
     * @return void
     */
    public function modifyTitle($iTitle, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $data = array("title" => $iTitle);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    /**
     * Room::ModifyMinMaxPlayer()
     * 
     * 修改房間 玩家人數上下限
     * 
     * @param mixed $minPlayer
     * @param mixed $maxPlayer
     * @param mixed $cKey
     * @return void
     */
    public function ModifyMinMaxPlayer($minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        checkPlayerNumber($minPlayer, $maxPlayer);

        $data = array("min" => $minPlayer, "max" => $maxPlayer);
        $this->RoomModel->modify($roomId, $data);
        $this->out->save("ModifyTitle", true);
    }

    /**
     * Room::checkPlayerNumber()
     * 
     * 遊戲設定玩家人數上下限 防呆機制
     * 
     * @param mixed $minPlayer
     * @param mixed $maxPlayer
     * @return void
     */
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 1 && $minPlayer <= $maxPlayer && $maxPlayer <= 10))
        {
            $this->out->wrong("MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer");
        }
    }
}

?>