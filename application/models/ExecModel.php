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
        print_r($room);
        $roomPlayers = $rooms->playerInfo($roomId, $out);
        echo count($roomPlayers);
        if ($room[0]["max"] >= count($roomPlayers) && $room[0]["min"] <= count($roomPlayers))
        {
            $data = array("status" => "start", "turn" => $this->createRoomPlayerStatus($roomPlayers));
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
    
    // �гy ���a�C�������A��
    // turn( P => Playing, E => Ending, N => Now )
    private function createRoomPlayerStatus($roomPlayers)
    {
        $playerStatus = "";
        foreach($roomPlayers as $roomPlayer)
        {
            $playerStatus .= "P";
        }
        $playerStatus[0] = "N";
        return $playerStatus;
    }
}

?>