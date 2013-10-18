<?php

class ExecModel extends CI_Model
{
    /**
     * ExecModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * ExecModel::start()
     * 
     * 遊戲開始
     * 
     * @param mixed $userId
     * @param mixed $roomId
     * @param mixed $out
     * @param mixed $rooms
     * @return void
     */
    public function start($userId, $roomId, $out, $rooms)
    {
        $this->db->trans_begin();
        $room = $rooms->roomInfo($out, $roomId);
        $out->delete("Room");
        $roomPlayers = $rooms->playerInfo($roomId, $out);

        if (count($room) <= 0)
        { // 房間不存在
            $this->db->trans_rollback();
            $out->wrong("Room isn't Exist");
        }

        if ($room[0]["max"] >= count($roomPlayers) && $room[0]["min"] <= count($roomPlayers))
        { // 確認房間內玩家 人數是否符合
            if ($roomPlayers[0]["userId"] != $userId)
            { // 確認是否為室長
                $this->db->trans_rollback();
                $out->wrong("Participants Cannot Open Room");
            }

            // 寫入開房資料
            $data = array("status" => "start", "turn" => $roomPlayers[0]["userId"],
                "playingList" => $this->roomPlayersToString($roomPlayers));
            $this->db->where("id", $roomId);
            $this->db->update("gameroom", $data);

            if ($this->db->trans_status() === false)
                $this->db->trans_rollback();
            else
                $this->db->trans_commit();
        }
        else
        {
            $this->db->trans_rollback();
            $out->wrong("Opening Room Standard is Not Satisfied");
        }
    }

    /**
     * ExecModel::roomPlayersToString()
     * 
     * 把 RoomPlayer 轉成 String
     * 
     * @param mixed $roomPlayers
     * @return
     */
    private function roomPlayersToString($roomPlayers)
    {
        $ids = array();
        foreach ($roomPlayers as $roomPlayer)
        {
            array_push($ids, $roomPlayer["userId"]);
        }
        return implode("-", $ids);
    }

    /**
     * ExecModel::send()
     * 
     * 送訊息至房間中
     * 
     * @param mixed $message
     * @param mixed $userId
     * @param mixed $roomId
     * @return void
     */
    public function send($message, $userId, $roomId)
    {
        $this->db->trans_begin();
        $this->db->select_max("count");
        $this->db->from("command");
        $this->db->where("roomId", $roomId);
        $result = $this->db->get()->result();
        $now = count($result) > 0 ? $result[0]->count + 1 : 1;
        $data = array("roomId" => $roomId, "userId" => $userId, "timestamp" => time(),
            "count" => $now, "order" => $message);
        $this->db->insert("command", $data);

        if ($this->db->trans_status() === false)
            $this->db->trans_rollback();
        else
            $this->db->trans_commit();
    }

    /**
     * ExecModel::checkRoomIsStart()
     * 
     * 確保此房間是 遊戲中 且存在
     * 
     * @param mixed $roomInfos
     * @param mixed $out
     * @return void
     */
    public function checkRoomIsStart($roomInfos, $out)
    {
        if (count($roomInfos) <= 0)
        {
            $out->wrong("Cannot Send Message To Room");
        }
    }

    /**
     * ExecModel::next()
     * 
     * 得到下位玩家
     * 
     * @param mixed $roomInfos
     * @param mixed $userId
     * @param mixed $out
     * @return
     */
    public function next($roomInfos, $userId, $out)
    {
        // 計算下一位玩家
        $roomInfo = $roomInfos[0];
        $turn = $roomInfo["turn"];
        $list = explode("-", $roomInfo["list"]);
        for ($i = 0; $i < count($list); $i++)
        {
            if ($list[$i] == $userId)
            {
                $nextPlayer = $list[($i + 1) % count($list)];
                break;
            }
        }
        
        // 更新資料庫
        $data = array("turn"=>$nextPlayer);
        $this->db->where("id", $roomInfo["id"]);
        $this->db->update("gameroom", $data);
        
        return $nextPlayer;
    }
}

?>