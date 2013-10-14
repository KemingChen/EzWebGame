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

    public function checkLogin($account, $password)
    {
        $this->db->select("id, userName");
        $this->db->from('user');
        $this->db->where('account', $account);
        $this->db->where('password', $password);
        $result = $this->db->get()->result();

        $hasLogin = count($result) > 0;
        return $hasLogin ? array("id" => $result[0]->id, "name" => $result[0]->userName) : false;
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

        $key = sprintf("%s*%d*%d*%d", $Ckey, $uerId, $gameId, $roomId);
        $this->saveKey($uerId, $gameId, $key);
        return $key;
    }

    private function saveKey($uerId, $gameId, $key)
    {
        $this->db->select("id");
        $this->db->from('auth');
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $result = $this->db->get()->result();

        if (count($result) > 0)
        {

        }
        else
        {

        }
        $data = array('title' => 'My title', 'name' => 'My Name', 'date' => 'My date');

        $this->db->insert('mytable', $data);
    }
}

?>