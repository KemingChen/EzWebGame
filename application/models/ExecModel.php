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
        if ($room[0]["max"] >= count($roomPlayers) && $room[0]["min"] <= count($roomPlayers))
        {
            $ps = $this->createPS($roomPlayers);
            print_r($ps);
            $sPS = $this->PStoString($ps);
            echo $sPS;
            $data = array("status" => "start", "turn" => $sPS);
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

    // 創造 玩家遊戲中狀態表(PS)
    // turn( P => Playing, E => Ending, N => Now )
    private function createPS($roomPlayers)
    {
        $ps = array();
        for ($i = 0; $i < count($roomPlayers); $i++)
        {
            $ps[$i] = array($i == 0 ? "N" : "P", $roomPlayers[$i]["userId"]);
        }
        return $ps;
    }

    // 換下一位玩家 並傳回 下一位玩家在PS中的位置
    private function nextPS($ps)
    {
        for ($i = 0; $i < count($ps); $i++)
        {
            if ($ps[$i] == "N")
            {
                $ps[$i] = "P";
                $next = ($i + 1) % count($ps);
                $ps[$next] = "N";
                return $next;
            }
        }
    }

    // 把PS轉成字串
    private function PStoString($ps)
    {
        $sPS = "";
        for ($i = 0; $i < count($ps); $i++)
        {
            if ($i != 0)
                $sPS .= "-";
            $sPS .= $ps[$i][0] . "&" . $ps[$i][1];
        }
        return $sPS;
    }

    // 把字串轉成PS
    private function parsePS($sPS)
    {
        $ps = explode("-", $sPS);
        foreach ($ps as & $value)
        {
            $value = explode("&", $value);
        }
        return $ps;
    }
}

?>