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
}
?>