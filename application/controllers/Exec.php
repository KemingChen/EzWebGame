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

    // 開始遊戲
    public function start($cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->load->model("RoomModel", "room");
        $this->ExecModel->start($userId, $roomId, $this->out, $this->room);
        $this->out->save("Start", true);
        $this->out->show();
    }

    // 遊戲中 傳送指令給其他玩家
    /**
     * Exec::SendMessage()
     * 
     * @param mixed $message
     * @param mixed $cKey
     * @return void
     */
    public function SendMessage($message, $cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        // 確保此房間是 遊戲中 且存在
        $this->load->model("RoomModel", "room");
        $room = $this->room->roomInfo($this->out, $roomId, "start");
        if (count($room) <= 0)
        {
            $this->out->wrong("Cannot Send Message To Room");
        }

        // 確保現在是輪到自己送訊息
        if ($room[0]["turn"] == $userId)
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
}

?>