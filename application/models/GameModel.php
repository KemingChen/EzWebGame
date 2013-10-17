<?php

class GameModel extends CI_Model
{
    /**
     * GameModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * GameModel::create()
     * 
     * 創建遊戲
     * 
     * @param mixed $name
     * @param mixed $gKey
     * @param mixed $password
     * @return
     */
    public function create($name, $gKey, $password)
    {
        $data = array('gameName' => $name, 'gKey' => $gKey, 'password' => $password);
        $this->db->insert('gameinfo', $data);
        return $gKey;
    }

    /**
     * GameModel::exist()
     * 
     * 確認某欄資料在 user資料表 中是否存在
     * 
     * @param mixed $field
     * @param mixed $value
     * @return
     */
    public function exist($field, $value)
    {
        $this->db->select($field);
        $this->db->from('gameinfo');
        $this->db->where($field, $value);
        return $this->db->count_all_results() > 0;
    }

    /**
     * GameModel::getGameKey()
     * 
     * 使用遊戲名稱 跟 開發者密碼 得到gKey
     * 
     * @param mixed $name
     * @param mixed $password
     * @return
     */
    public function getGameKey($name, $password)
    {
        $this->db->select("gKey");
        $this->db->from('gameinfo');
        $this->db->where('gameName', $name);
        $this->db->where('password', $password);
        $result = $this->db->get()->result();
        return count($result) > 0 ? $result[0]->gKey : 0;
    }

    /**
     * GameModel::checkAuth()
     * 
     * 確認 gKey
     * 
     * @param mixed $gKey
     * @return
     */
    public function checkAuth($gKey)
    {
        $this->db->select("id, gameName");
        $this->db->from('gameinfo');
        $this->db->where('gKey', $gKey);
        $result = $this->db->get()->result();

        $isPermit = count($result) > 0;
        return $isPermit ? array("id" => $result[0]->id, "name" => $result[0]->gameName) : false;
    }
}

?>