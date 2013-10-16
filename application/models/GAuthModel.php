<?php

class GAuthModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // �x�s�n�J Key
    public function saveLoginKey($loginKey)
    {
        list($key, $gameId) = explode('_', $loginKey);
        $data = array('gameId' => $gameId, 'key' => $loginKey);

        $this->db->insert('gauth', $data);
    }

    // �T�{�n�J Key
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

    // �R���n�J Key
    public function deleteLoginKey($loginKey)
    {
        list($key, $gameId) = explode('_', $loginKey);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $loginKey);
        $this->db->delete('gauth');
    }
}

?>