<?php

class Exec extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("ExecModel");
        $this->load->model("AuthModel");
    }

    /**
     * Exec::start()
     * 
     * 開始遊戲
     * 
     * @param mixed $cKey
     * @return void
     */
    public function start($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->load->model("RoomModel", "room");
        $this->ExecModel->start($userId, $roomId, $this->out, $this->room);
        $this->out->save("Start", true);
        $this->out->show();
    }

    /**
     * Exec::SendMessage()
     * 
     * 遊戲中 傳送指令給其他玩家
     * 
     * @param mixed $message
     * @param mixed $cKey
     * @return void
     */
    public function SendMessage($message, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        $this->ExecModel->send($message, $userId, $roomId, $roomPlayers);
        $this->out->save("Message", $message);

        $this->out->show();
    }

    /**
     * Exec::nextRound()
     * 
     * @param mixed $cKey
     * @return void
     */
    public function nextRound($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $playerId = $this->ExecModel->next($roomInfo, $userId, $roomId);
        $this->out->save("NextRound", $playerId);

        $this->out->show();
    }

    /**
     * Exec::checkRoomExistAndIsTurnMe()
     * 
     * @param mixed $userId
     * @param mixed $roomId
     * @return 傳回目前房間資訊
     */
    private function checkRoomExistAndIsTurnMe($userId, $roomId)
    {
        // 確認房間
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->out->delete("Room"); // 刪除儲存在out中的Room Key
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);

        // 確認現在是輪到自己送訊息
        if ($roomInfos[0]["turn"] == $userId)
        {
            return $roomInfos[0];
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }
    }
}

?>