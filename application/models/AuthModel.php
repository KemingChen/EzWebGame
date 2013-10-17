<?php

class AuthModel extends CI_Model
{
    /**
     * AuthModel::__construct()
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * AuthModel::getNextCommuKey()
     * 
     * �T�{�q�T Key �ò��ͤU�@�ճq�T Key ���x�s�_��
     * 
     * @param mixed $cKey
     * @param mixed $out
     * @return
     */
    public function getNextCommuKey($cKey, $out)
    {
        if ($this->checkCommuKey($cKey))
        {
            list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
            $nextCKey = $this->commuKeygen($userId, $gameId, $roomId);
            $this->saveCommuKey($userId, $gameId, $nextCKey);
            $out->save("cKey", $nextCKey);
            return $nextCKey;
        }
        $out->wrong("Communication Key Deny");
    }

    /**
     * AuthModel::editCommuKey()
     * 
     * �ק﷾�qKey���ұa�ж���T
     * 
     * @param mixed $cKey
     * @param mixed $iRoomId
     * @param mixed $out
     * @return
     */
    public function editCommuKey($cKey, $iRoomId, $out)
    {
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $nextCKey = $this->commuKeygen($userId, $gameId, $iRoomId);
        $this->saveCommuKey($userId, $gameId, $nextCKey);
        $out->save("cKey", $nextCKey);
        return $nextCKey;
    }

    /**
     * AuthModel::commuKeygen()
     * 
     * �q�T Key ���;�
     * 
     * @param mixed $userId
     * @param mixed $gameId
     * @param mixed $roomId
     * @return
     */
    public function commuKeygen($userId, $gameId, $roomId)
    {
        $key = $this->keygen(10);
        $cKey = sprintf("%s_%d_%d_%d", $key, $userId, $gameId, $roomId);
        return $cKey;
    }

    /**
     * AuthModel::saveCommuKey()
     * 
     * �x�s�q�T Key
     * 
     * @param mixed $userId
     * @param mixed $gameId
     * @param mixed $key
     * @return void
     */
    public function saveCommuKey($userId, $gameId, $key)
    {
        $data = array('userId' => $userId, 'gameId' => $gameId, 'key' => $key);

        $this->db->select("id");
        $this->db->from('auth');
        $this->db->where('userId', $userId);
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

    /**
     * AuthModel::deleteCommuKey()
     * 
     * �R���q�T Key
     * 
     * @param mixed $cKey
     * @return void
     */
    public function deleteCommuKey($cKey)
    {
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->db->where('userId', $userId);
        $this->db->where('gameId', $gameId);
        $this->db->delete('auth');
    }

    /**
     * AuthModel::checkCommuKey()
     * 
     * �ˬd�q�T Key �O�_�s�b
     * 
     * @param mixed $cKey
     * @return
     */
    public function checkCommuKey($cKey)
    {
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->db->select("key");
        $this->db->from('auth');
        $this->db->where('userId', $userId);
        $this->db->where('gameId', $gameId);
        $this->db->where('key', $cKey);
        $result = $this->db->get()->result();

        return count($result) > 0 ? true : false;
    }

    /**
     * AuthModel::keygen()
     * 
     * ���_���;�
     * 
     * @param mixed $length
     * @return
     */
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