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

    // 確認某欄資料在 user資料表 中是否存在
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
}

?>