<?php

class GAuthModel extends CI_Model
{
    /**
     * GAuthModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * GAuthModel::saveLoginKey()
     * 
     * 儲存登入 Key
     * 
     * @param mixed $loginKey
     * @return void
     */
    public function saveLoginKey($loginKey)
    {
        list($key, $gameId) = explode('_', $loginKey);
        $data = array('gameId' => $gameId, 'key' => $loginKey);

        $this->db->insert('gauth', $data);
    }

    /**
     * GAuthModel::checkLoginKey()
     * 
     * 確認登入 Key
     * 
     * @param mixed $loginKey
     * @return
     */
    public function checkLoginKey($loginKey)
    {
        list($key, $gameId) = explode('_', $loginKey);

        $this->db->select("id");
        $this->db->from('gauth');
        $this->db->where('key', $loginKey);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();

        return count($result) > 0 ? true : false;
    }

    /**
     * GAuthModel::deleteLoginKey()
     * 
     * 刪除登入 Key
     * 
     * @param mixed $loginKey
     * @return void
     */
    public function deleteLoginKey($loginKey)
    {
        list($key, $gameId) = explode('_', $loginKey);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $loginKey);
        $this->db->delete('gauth');
    }
}

?>