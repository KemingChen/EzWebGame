<?php

class RoomModel extends CI_Model
{
    /**
     * RoomModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * RoomModel::create()
     * 
     * 創立房間
     * 
     * @param mixed $gameId
     * @param mixed $title
     * @param mixed $minPlayer
     * @param mixed $maxPlayer
     * @return
     */
    public function create($gameId, $title, $minPlayer, $maxPlayer)
    {
        $data = array('gameId' => $gameId, 'title' => $title, 'min' => $minPlayer, 'max' =>
            $maxPlayer, 'status' => 'wait');
        $this->db->insert('gameroom', $data);
        return $this->db->insert_id();
    }

    /**
     * RoomModel::join()
     * 
     * 加入房間
     * 
     * @param mixed $userId
     * @param mixed $roomId
     * @param mixed $out
     * @return
     */
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

    /**
     * RoomModel::leave()
     * 
     * 離開房間
     * 
     * @param mixed $userId
     * @param mixed $roomId
     * @param mixed $out
     * @return void
     */
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

    /**
     * RoomModel::roomInfo()
     * 
     * 未開始房間資訊
     * 
     * @param mixed $out
     * @param bool $roomId
     * @param string $status
     * @return
     */
    public function roomInfo($out, $roomId = false, $status = "wait")
    {
        $result = $this->getRooms($out, $roomId, $status);

        $room = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["id"] = $row->id;
            $array["title"] = $row->title;
            $array["max"] = $row->max;
            $array["min"] = $row->min;
            $array["now"] = $row->now == null ? 0 : $row->now;
            $array["turn"] = $row->turn;
            $array["list"] = $row->playingList;

            if ($row->id != null)
                array_push($room, $array);
        }
        $out->save("Room", $room);
        return $room;
    }

    /**
     * RoomModel::playerInfo()
     * 
     * 房間中玩家資訊
     * 
     * @param mixed $roomId
     * @param mixed $out
     * @return
     */
    public function playerInfo($roomId, $out)
    {
        $result = $this->getRoomPlayers($roomId);

        $players = array();
        foreach ($result as $row)
        {
            $array = array();
            $array["userId"] = $row->id;
            $array["userName"] = $row->userName;

            array_push($players, $array);
        }
        $out->save("Players", $players);
        return $players;
    }

    /**
     * RoomModel::getRooms()
     * 
     * 得到未開始房間(未處理成array物件)
     * 
     * @param mixed $out
     * @param mixed $roomId
     * @param mixed $status
     * @return
     */
    private function getRooms($out, $roomId, $status)
    {
        // 各房間中有多少人的 Table(RoomId, NowPlayers)
        $RoomPlayerCountTable =
            "(SELECT roomId, count(userId) AS now from room_to_user GROUP BY roomId) AS RPCT";

        $this->db->select("gameroom.id, title, min, max, turn, now, playingList");
        $this->db->from("gameroom");
        if ($roomId != false)
            $this->db->where("gameroom.id", $roomId);
        $this->db->where("status", $status);
        $this->db->join($RoomPlayerCountTable, "gameroom.id = RPCT.roomId", "left");
        return $this->db->get()->result();
    }

    /**
     * RoomModel::getRoomPlayers()
     * 
     * 得到房間中玩家資訊(未處理成array物件)
     * 
     * @param mixed $roomId
     * @return
     */
    private function getRoomPlayers($roomId)
    {
        $this->db->select("user.id, user.userName");
        $this->db->from("room_to_user");
        $this->db->where("room_to_user.roomId", $roomId);
        $this->db->join("user", "user.id = room_to_user.userId", "left");
        $this->db->order_by("room_to_user.id", "ASC");
        return $this->db->get()->result();
    }

    /**
     * RoomModel::modify()
     * 
     * 修改房間資訊
     * 
     * @param mixed $roomId
     * @param mixed $data
     * @return void
     */
    public function modify($roomId, $data)
    {
        $this->db->where("id", $roomId);
        $this->db->update('gameroom', $data);
    }

    /**
     * RoomModel::deleteSelfFromAnyRoom()
     * 
     * 刪除所有自己所在之任何房間
     * 
     * @param mixed $userId
     * @return void
     */
    public function deleteSelfFromAnyRoom($userId)
    {
        $this->db->where("userId", $userId);
        $this->db->delete("room_to_user");
    }

    /**
     * RoomModel::checkRoomCanJoin()
     * 
     * 確認此房間是否能加入
     * 
     * @param mixed $roomId
     * @param mixed $out
     * @return void
     */
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

    /**
     * RoomModel::checkUserNotInAnyRoom()
     * 
     * 確認玩家有無在其他房間內
     * 
     * @param mixed $userId
     * @param mixed $out
     * @return void
     */
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