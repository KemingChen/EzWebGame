<?php

class RoomModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 創立房間
    public function create($gameId, $title, $minPlayer, $maxPlayer)
    {
        $data = array('gameId' => $gameId, 'title' => $title, 'turn' => '', 'min' => $minPlayer,
            'max' => $minPlayer, 'status' => 'wait');
        $this->db->insert('gameroom', $data);
        return $this->db->insert_id();
    }

    // 加入房間
    public function join($userId, $roomId, $out)
    {
        $this->checkUserNotInAnyRoom($userId, $out); // 確認玩家不要重複加入房間
        $this->db->trans_begin();

        $this->checkRoomCanJoin($roomId, $out);
        $data = array("roomId" => $roomId, "userId" => $userId);
        $this->db->insert('room_to_user', $data);

        if ($this->db->trans_status() === false)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }

    // 確認此房間是否能加入
    private function checkRoomCanJoin($roomId, $out)
    {
        $this->db->select("max");
        $this->db->from('gameroom');
        $this->db->where('id', $roomId);
        $this->db->where('status', 'wait');
        $result = $this->db->get()->result();

        if (count($result) <= 0)
        {
            $this->db->trans_rollback();
            $out->wrong("No This Room or Status isn't Wait");
        }
        $max = $result[0]->max;

        $this->db->select("roomId");
        $this->db->from('room_to_user');
        $this->db->where('roomId', $roomId);
        $result = $this->db->get()->result();

        if (count($result) + 1 > $max)
        {
            $this->db->trans_rollback();
            $out->wrong("This Room is Full");
        }
    }

    // 確認玩家有無在其他房間內
    private function checkUserNotInAnyRoom($userId, $out)
    {
        $this->db->select("roomId");
        $this->db->from('room_to_user');
        $this->db->where('userId', $userId);
        $result = $this->db->get()->result();
        if (count($result) > 0)
        {
            $out->wrong(sprintf("User In %d Room", $result[0]->roomId));
        }
    }
}

?>