<?php

class ExecModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // �C���}�l
    public function start($userId, $roomId, $out, $rooms)
    {
        $this->db->trans_begin();
        $room = $rooms->roomInfo($out, $roomId);
        $roomPlayers = $rooms->playerInfo($roomId, $out);

        if (count($room) <= 0)
        { // �ж����s�b
            $this->db->trans_rollback();
            $out->wrong("Room isn't Exist");

        }

        if ($room[0]["max"] >= count($roomPlayers) && $room[0]["min"] <= count($roomPlayers))
        { // �T�{�ж������a �H�ƬO�_�ŦX
            if ($roomPlayers[0]["userId"] != $userId)
            { // �T�{�O�_���Ǫ�
                $this->db->trans_rollback();
                $out->wrong("Participants Cannot Open Room");
            }
            
            // �g�J�}�и��
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

    // �e�T���ܩж���
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