<?php

class GameModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 創建遊戲
    public function create($name, $password)
    {
        $gKey = $this->gKeygen();
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

        if ($iGameId != $gameId) //gameId 無法 match

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

    // gKey 產生器
    private function gKeygen()
    {
        $length = 18;
        $gKey = '';
        $microtime = microtime();

        list($usec, $sec) = explode(' ', $microtime);
        mt_srand((float)$sec + ((float)$usec * 100000));

        $inputs = array_merge(range('z', 'a'), range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++)
        {
            $gKey .= $inputs{mt_rand(0, 61)};
        }

        return $gKey;
    }
}

?>