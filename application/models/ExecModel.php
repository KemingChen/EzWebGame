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
     * 送事件至房間中的其他玩家
     * 
     * @param mixed $message
     * @param mixed $senderId
     * @param mixed $roomId
     * @param mixed $roomPlayers
     * @return void
     */
    public function send($type, $param, $senderId, $roomId, $roomPlayers)
    {
        if (count($roomPlayers) > 1)
        {
            $insertDatas = array();
            foreach ($roomPlayers as $roomPlayer)
            {
                if ($roomPlayer["userId"] != $senderId)
                {
                    $data = array();
                    $data["type"] = $type;
                    $data["receiverId"] = $roomPlayer["userId"];
                    $data["roomId"] = $roomId;
                    $data["param"] = $param;
                    array_push($insertDatas, $data);
                }
            }
            $this->db->insert_batch("event", $insertDatas);
        }
    }

    public function listen($userId, $roomId)
    {
        $this->db->select("id, type, param");
        $this->db->from("event");
        //$this->db->where("type", $type);
        $this->db->where("receiverId", $userId);
        $this->db->where("roomId", $roomId);
        $this->db->order_by("id", "ASC");
        $result = $this->db->get()->result();
        $array = array();
        $lastEventId = 0;
        foreach ($result as $row)
        {
            array_push($array, array("Type" => $row->type, "Param" => $row->param));
            $lastEventId = $row->id;
        }

        // 刪除已讀訊息
        if ($lastEventId != 0)
        {
            $this->db->where("receiverId", $userId);
            $this->db->where("id <=", $lastEventId);
            $this->db->delete("event");
        }
        return $array;
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
     * 把回合控制器中的 turn 轉到下一位玩家
     * 
     * @param mixed $roomInfos
     * @param mixed $userId
     * @param mixed $out
     * @return
     */
    public function next($roomInfo, $userId, $out)
    {
        // 計算下一位玩家
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
        $data = array("turn" => $nextPlayer);
        $this->db->where("id", $roomInfo["id"]);
        $this->db->update("gameroom", $data);

        return $nextPlayer;
    }

    /**
     * ExecModel::removeFromPlayingList()
     * 
     * 把自己從回合控制器中移除
     * 
     * @param mixed $userId
     * @param mixed $roomInfo
     * @return void
     */
    public function removeFromPlayingList($userId, $roomInfo)
    {
        $roomId = $roomInfo["id"];
        $list = explode("-", $roomInfo["list"]);
        for ($i = 0; $i < count($list); $i++)
        {
            if ($list[$i] == $userId)
            {
                unset($list[$i]);
                break;
            }
        }
        $data = array("playingList" => implode("-", $list));
        $this->db->where("id", $roomId);
        $this->db->update("gameroom", $data);
    }
}

?>