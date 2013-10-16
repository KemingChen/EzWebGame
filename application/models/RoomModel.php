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
            return $roomId;
        }
    }

    // 離開房間
    public function leave($userId, $roomId, $out)
    {
        $this->db->where("roomId", $roomId);
        $this->db->where("userId", $userId);
        $this->db->delete('room_to_user');
        $this->db->trans_begin();
        $plasyers = $this->getRoomPlayers($roomId);
        if (count($plasyers) <= 0)
        {
            $this->db->where("id", $roomId);
            $this->db->delete("gameroom");
        }
        
        if ($this->db->trans_status() === false)
            $this->db->trans_rollback();
        else
            $this->db->trans_commit();
    }

    // 未開始房間資訊
    public function roomInfo($out, $roomId = false)
    {
        $result = $this->getRooms($out, $roomId);
        
        $room = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["id"] = $row->id;
            $array["title"] = $row->title;
            $array["max"] = $row->max;
            $array["min"] = $row->min;
            $array["now"] = $row->now;
            
            if($row->id != null)
                array_push($room, $array);
        }
        $out->save("Room", $room);
        return $room;
    }

    // 房間中玩家資訊
    public function playerInfo($roomId, $out)
    {
        $result = $this->getRoomPlayers($roomId);
        
        $player = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["userId"] = $row->id;
            $array["userName"] = $row->userName;

            array_push($player, $array);
        }
        $out->save("Player", $player);
        return $player;
    }

    // 得到未開始房間(未處理成array物件)
    private function getRooms($out, $roomId)
    {
        $this->db->select("gameroom.id, title, min, max, count(room_to_user.id) as now");
        $this->db->from("gameroom");
        if ($roomId != false)
            $this->db->where("gameroom.id", $roomId);
        $this->db->where("status", "wait");
        $this->db->join("room_to_user", "gameroom.id = room_to_user.roomId", "left");
        return $this->db->get()->result();
    }

    // 得到房間中玩家資訊(未處理成array物件)
    private function getRoomPlayers($roomId)
    {
        $this->db->select("user.id, user.userName");
        $this->db->from("room_to_user");
        $this->db->where("room_to_user.roomId", $roomId);
        $this->db->join("user", "user.id = room_to_user.userId", "left");
        $this->db->order_by("room_to_user.id", "ASC");
        return $this->db->get()->result();
    }

    // 修改房間資訊
    public function modify($roomId, $data)
    {
        $this->db->where("id", $roomId);
        $this->db->update('gameroom', $data);
    }

    // 確認此房間是否能加入
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

    // 確認玩家有無在其他房間內
    public function checkUserNotInAnyRoom($userId, $out)
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