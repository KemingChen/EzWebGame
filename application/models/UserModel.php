<?php

class UserModel extends CI_Model
{
    //parent::__construct();
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create($name, $account, $password)
    {
        $data = array('userName' => $name, 'account' => $account, 'password' => $password);
        $this->db->insert('user', $data);
        return $this->db->insert_id();
    }

    public function exist($field, $value)
    {
        $this->db->select($field);
        $this->db->from('user');
        $this->db->where($field, $value);
        return $this->db->count_all_results() > 0;
    }

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

    public function keygen($uerId, $gameId, $roomId)
    {
        $length = 10;
        $Ckey = '';
        $microtime = microtime();

        list($usec, $sec) = explode(' ', $microtime);
        mt_srand((float)$sec + ((float)$usec * 100000));

        $inputs = array_merge(range('z', 'a'), range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++)
        {
            $Ckey .= $inputs{mt_rand(0, 61)};
        }

        $key = sprintf("%s_%d_%d_%d", $Ckey, $uerId, $gameId, $roomId);
        $this->saveKey($uerId, $gameId, $key);
        return $key;
    }

    private function saveKey($uerId, $gameId, $key)
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

    public function deleteKey($key)
    {
        list($CKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $this->db->delete('auth');
    }

    public function checkKey($key)
    {
        list($CKey, $uerId, $gameId, $roomId) = explode('_', $key);
        $this->db->select("key");
        $this->db->from('auth');
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();
        
        if (count($result) > 0)
        {
            //echo $result[0]->key."<br />";
            return $result[0]->key == $key ? true : false;
        }
        return false;
    }
}

?>