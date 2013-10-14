<?php

class GameModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //遊戲資訊確認
    public function checkAuth($gameId, $gKey)
    {
        $this->db->select("id, gameName");
        $this->db->from('gameinfo');
        $this->db->where('id', $gameId);
        $this->db->where('gKey', $gKey);
        $result = $this->db->get()->result();

        $isPermit = count($result) > 0;
        return $isPermit ? array("id" => $result[0]->id, "name" => $result[0]->gameName) : false;
    }

    //儲存Key給 使用者遊戲登入使用
    public function saveKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $data = array('gameId' => $gameId, 'key' => $key);

        $this->db->insert('gauth', $data);
    }

    //確認key是否可給使用者 登入此遊戲用
    public function checkKey($id, $key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);

        if ($id != $gameId)//遊戲Id不同
            return false;

        $this->db->select("id");
        $this->db->from('gauth');
        $this->db->where('key', $key);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();

        return count($result) > 0 ? true : false;
    }

    //刪除此遊戲登入key
    public function deleteKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $key);
        $this->db->delete('gauth');
    }
}

?>