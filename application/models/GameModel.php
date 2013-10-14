<?php

class GameModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 創建遊戲
    public function create($name, $gKey, $password)
    {
        $data = array('gameName' => $name, 'gKey' => $gKey, 'password' => $password);
        $this->db->insert('gameinfo', $data);
        return $gKey;
    }

    // 確認某欄資料在 user資料表 中是否存在
    public function exist($field, $value)
    {
        $this->db->select($field);
        $this->db->from('gameinfo');
        $this->db->where($field, $value);
        return $this->db->count_all_results() > 0;
    }

    public function getGameKey($name, $password)
    {
        $this->db->select("gKey");
        $this->db->from('gameinfo');
        $this->db->where('gameName', $name);
        $this->db->where('password', $password);
        $result = $this->db->get()->result();
        return count($result) > 0 ? $result[0]->gKey : 0;
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
}

?>