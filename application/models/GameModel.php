<?php

class GameModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 確認 gKey 與 game 可以 match
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

    // 儲存key 給使用者登入此遊戲用
    public function saveKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $data = array('gameId' => $gameId, 'key' => $key);

        $this->db->insert('gauth', $data);
    }

    // 確認key 可給使用者 登入此遊戲用
    public function checkKey($iGameId, $key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);

        if ($iGameId != $gameId)//gameId 無法 match
            return false;

        $this->db->select("id");
        $this->db->from('gauth');
        $this->db->where('key', $key);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();

        return count($result) > 0 ? true : false;
    }

    // 刪除給此遊戲登入用的 key
    public function deleteKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $key);
        $this->db->delete('gauth');
    }
}

?>