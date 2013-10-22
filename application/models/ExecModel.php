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
    public function start($userId, $roomId, $out, $roomInfo, $roomPlayers)
    {
        //$this->db->trans_begin();

        if (count($roomInfo) <= 0)
        { // �ж����s�b
            //$this->db->trans_rollback();
            $out->wrong("Room isn't Exist");
        }

        if ($roomInfo[0]["max"] >= count($roomPlayers) && $roomInfo[0]["min"] <= count($roomPlayers))
        { // �T�{�ж������a �H�ƬO�_�ŦX
            if ($roomPlayers[0]["userId"] != $userId)
            { // �T�{�O�_���Ǫ�
                //$this->db->trans_rollback();
                $out->wrong("Participants Cannot Open Room");
            }

            // �g�J�}�и��
            $data = array("status" => "start", "turn" => $roomPlayers[0]["userId"],
                "playingList" => $this->roomPlayersToString($roomPlayers));
            $this->db->where("id", $roomId);
            $this->db->update("gameroom", $data);

            /*if ($this->db->trans_status() === false)
            $this->db->trans_rollback();
            else
            $this->db->trans_commit();*/

            return $roomPlayers[0];
        }
        else
        {
            //$this->db->trans_rollback();
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
     * �e�ƥ�ܩж�������L���a
     * 
     * @param mixed $message
     * @param mixed $senderId
     * @param mixed $roomId
     * @param mixed $roomPlayers
     * @return void
     */
    public function send($type, $param, $senderId, $roomId, $roomPlayers, $isIncludeSelf = false)
    {
        $insertDatas = array();
        $insertLogs = array();
        foreach ($roomPlayers as $roomPlayer)
        {
            if ($roomPlayer["userId"] != $senderId || $isIncludeSelf)
            {
                $data = array();
                $data["type"] = $type;
                $data["receiverId"] = $roomPlayer["userId"];
                $data["roomId"] = $roomId;
                $data["param"] = $param;
                array_push($insertDatas, $data);

                $log = array();
                $log["value"] = json_encode($data);
                $log["time"] = date("Y-m-d H:i:s");
                array_push($insertLogs, $log);
            }
        }
        if (count($insertDatas) > 0)
        {
            $this->db->insert_batch("event", $insertDatas);
            $this->db->insert_batch("log", $insertLogs);
        }
    }

    public function listen($userId, $roomId, $out, $roomModel)
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
            switch ($row->type)
            {
                case 'roomChanged':
                    $param = array("Players" => $roomModel->playerInfo($roomId, $out));
                    break;
                default:
                    $param = $row->param;
            }
            array_push($array, $out->convertToEvent($row->type, $param));
            $lastEventId = $row->id;
        }

        // �R���wŪ�T��
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
     * �T�O���ж��O �C���� �B�s�b
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
     * ��^�X������� turn ���U�@�쪱�a
     * 
     * @param mixed $roomInfos
     * @param mixed $userId
     * @param mixed $out
     * @return
     */
    public function next($roomInfo, $userId, $out)
    {
        // �p��U�@�쪱�a
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

        // ��s��Ʈw
        $data = array("turn" => $nextPlayer);
        $this->db->where("id", $roomInfo["id"]);
        $this->db->update("gameroom", $data);

        return $nextPlayer;
    }

    /**
     * ExecModel::removeFromPlayingList()
     * 
     * ��ۤv�q�^�X���������
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