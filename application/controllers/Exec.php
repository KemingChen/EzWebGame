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
        
        // 獲取房間資料
        $this->load->model("RoomModel", "room");
        $roomInfo = $this->room->roomInfo($this->out, $roomId);
        $this->out->delete("Room");
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        
        $turnPlayer = $this->ExecModel->start($userId, $roomId, $this->out, $roomInfo, $roomPlayers);
        
        // 告知其他玩家 遊戲開始
        $message = sprintf("Room[%d] Start Game", $roomId);
        $this->ExecModel->send("start", $message, $userId, $roomId, $roomPlayers);
        
        // 告知所有玩家 現在換誰
        $message = json_encode($turnPlayer);
        $this->ExecModel->send("turn", $message, $userId, $roomId, $roomPlayers, true);
        
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
        $this->ExecModel->send("message", $message, $userId, $roomId, $roomPlayers);
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
     * Exec::ArriveFinalStep()
     * 
     * 告訴其他玩家 你已經獲勝 並把自己從回合控制器中移除
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ArriveFinalStep($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $this->ExecModel->next($roomInfo, $userId, $roomId);
        $this->ExecModel->removeFromPlayingList($userId, $roomInfo);
        $this->out->save("ArriveFinalStep", true);

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