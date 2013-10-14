<?php

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 創建使用者
    public function create($name, $account, $password)
    {
        $data = array('userName' => $name, 'account' => $account, 'password' => $password);
        $this->db->insert('user', $data);
        return $this->db->insert_id();
    }

    // 確認某欄資料是否存在
    public function exist($field, $value)
    {
        $this->db->select($field);
        $this->db->from('user');
        $this->db->where($field, $value);
        return $this->db->count_all_results() > 0;
    }

    // 確認使用者帳號密碼
    public function checkAuth($account, $password)
    {
        $this->db->select("id, userName");
        $this->db->from('user');
        $this->db->where('account', $account);
        $this->db->where('password', $password);
        $result = $this->db->get()->result();

        $isPermit = count($result) > 0;
        return $isPermit ? array("id" => $result[0]->id, "name" => $result[0]->userName) : false;
    }

    // 金鑰產生器
    public function keygen($uerId, $gameId, $roomId)
    {
        $length = 10;
        $cKey = '';
        $microtime = microtime();

        list($usec, $sec) = explode(' ', $microtime);
        mt_srand((float)$sec + ((float)$usec * 100000));

        $inputs = array_merge(range('z', 'a'), range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++)
        {
            $cKey .= $inputs{mt_rand(0, 61)};
        }

        $key = sprintf("%s_%d_%d_%d", $cKey, $uerId, $gameId, $roomId);
        return $key;
    }

    // 儲存Key 給下次溝通用
    public function saveKey($uerId, $gameId, $key)
    {
        $data = array('userId' => $uerId, 'gameId' => $gameId, 'key' => $key);

        $this->db->select("id");
        $this->db->from('auth');
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();
        
        //echo count($result) > 0 ? "count > 0;" : "count <= 0";
        
        if (count($result) > 0)
        {
            //echo $result[0]->id .";";
            //print_r($data);
            $this->db->where('id', $result[0]->id);
            $this->db->update('auth', $data);
            //echo $this->db->last_query();
        }
        else
        {
            $this->db->insert('auth', $data);
        }
    }

    // 刪除此溝通 key
    public function deleteKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $this->db->delete('auth');
    }

    // 檢查此溝通 key 是否存在
    public function checkKey($key)
    {
        list($cKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->select("key");
        $this->db->from('auth');
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $key);
        $result = $this->db->get()->result();
        
        return count($result) > 0 ? true : false;
    }
}

?>