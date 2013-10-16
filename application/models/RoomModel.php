<?php

class RoomModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // �Хߩж�
    public function create($gameId, $title, $minPlayer, $maxPlayer)
    {
        $data = array('gameId' => $gameId, 'title' => $title, 'turn' => '', 'min' => $minPlayer,
            'max' => $minPlayer, 'status' => 'wait');
        $this->db->insert('gameroom', $data);
        return $this->db->insert_id();
    }

    // �[�J�ж�
    public function join($userId, $roomId, $out)
    {
        $this->checkUserNotInAnyRoom($userId, $out); // �T�{���a���n���ƥ[�J�ж�
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
            return $roomId;
        }
    }

    // ���}�ж�
    public function leave($userId, $roomId, $out)
    {
        $this->db->where("roomId", $roomId);
        $this->db->where("userId", $userId);
        $this->db->delete('room_to_user');
    }

    // �ж���T
    public function roomInfo($gameId, $out, $roomId = false)
    {
        $this->db->select("gameroom.id, title, max, count(room_to_user.id) as now");
        $this->db->from("gameroom");
        if ($roomId != false)
            $this->db->where("gameroom.id", $roomId);
        $this->db->where("status", "wait");
        $this->db->join("room_to_user", "gameroom.id = room_to_user.roomId", "left");
        $result = $this->db->get()->result();
        
        $room = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["id"] = $row->id;
            $array["title"] = $row->title;
            $array["max"] = $row->max;
            $array["now"] = $row->now;
            
            array_push($room, $array);
        }
        $out->save("Room", $room);
    }

    // ���a��T
    public function playerInfo($roomId, $out)
    {
        $this->db->select("user.id, user.userName");
        $this->db->from("room_to_user");
        $this->db->where("room_to_user.id", $roomId);
        $this->db->join("user", "user.id = room_to_user.userId", "left");
        $result = $this->db->get()->result();
        
        $player = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["userId"] = $row->id;
            $array["userName"] = $row->userName;
            
            array_push($player, $array);
        }
        $out->save("Player", $player);
    }

    // �ק�ж���T
    public function modify($roomId, $data)
    {
        $this->db->where("id", $roomId);
        $this->db->update('gameroom', $data);
    }

    // �T�{���ж��O�_��[�J
    public function checkRoomCanJoin($roomId, $out)
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

    // �T�{���a���L�b��L�ж���
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