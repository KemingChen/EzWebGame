<?php

class ExecModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 遊戲開始
    public function start($userId, $roomId, $out, $rooms)
    {
        $this->db->trans_begin();
        $room = $rooms->roomInfo($out, $roomId);
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
                "playerList" => implode("-", $roomPlayers));
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

    // 送訊息至房間中
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
}

?>