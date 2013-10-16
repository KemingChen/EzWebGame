<?php

class AuthModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // �T�{�q�T Key �ò��ͤU�@�ճq�T Key ���x�s�_��
    public function getNextCommuKey($cKey)
    {
        if ($this->checkCommuKey($cKey))
        {
            list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
            $cKey = $this->commuKeygen($uerId, $gameId, $roomId);
            $this->saveCommuKey($uerId, $gameId, $cKey);
            return $cKey;
        }
        echo "Communication Key Error";
        exit;
    }

    // �q�T Key ���;�
    public function commuKeygen($uerId, $gameId, $roomId)
    {
        $key = $this->keygen(10);
        $cKey = sprintf("%s_%d_%d_%d", $key, $uerId, $gameId, $roomId);
        return $cKey;
    }

    // �x�s�q�T Key
    public function saveCommuKey($uerId, $gameId, $key)
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

    // �R���q�T Key
    public function deleteCommuKey($cKey)
    {
        list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $this->db->delete('auth');
    }

    // �ˬd�q�T Key �O�_�s�b
    public function checkCommuKey($cKey)
    {
        list($key, $uerId, $gameId, $roomId) = explode('_', $cKey);
        $this->db->select("key");
        $this->db->from('auth');
        $this->db->where('userId', $uerId);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $cKey);
        $result = $this->db->get()->result();

        return count($result) > 0 ? true : false;
    }

    // ���_���;�
    public function keygen($length)
    {
        $key = '';
        $microtime = microtime();

        list($usec, $sec) = explode(' ', $microtime);
        mt_srand((float)$sec + ((float)$usec * 100000));

        $inputs = array_merge(range('z', 'a'), range(0, 9), range('A', 'Z'));

        for ($i = 0; $i < $length; $i++)
        {
            $key .= $inputs{mt_rand(0, 61)};
        }

        return $key;
    }
}

?>