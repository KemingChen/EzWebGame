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
        
        // 確認房間
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);

        // 確認現在是輪到自己送訊息
        if ($roomInfos[0]["turn"] == $userId)
        {
            $this->ExecModel->send($message, $userId, $roomId);
            $this->out->save("Message", $message);
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }

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
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        // 確認房間
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->out->delete("Room");// 刪除儲存在out中的Room Key
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);
        
        // 確認現在是輪到自己送訊息
        if ($roomInfos[0]["turn"] == $userId)
        {
            $playerId = $this->ExecModel->next($roomInfos, $userId, $roomId);
            $this->out->save("NextRound", $playerId);
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }

        $this->out->show();
    }
}

?>