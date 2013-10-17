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
     * �C���}�l
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
     * �� RoomPlayer �ন String
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
     * �e�T���ܩж���
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
     * �T�O���ж��O �C���� �B�s�b
     * 
     * @param mixed $room
     * @param mixed $roomId
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

    public function next($playerInfos, $roomInfos, $userId, $out)
    {
        $roomInfo = $roomInfos[0];
        $turn = $roomInfo["turn"];
        $list = explode("-", $roomInfo["list"]);
        foreach ($list as $player)
        {
            //if ()
            if ($player == $userId)
            {
                
            }
        }
    }
}

?>