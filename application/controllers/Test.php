<?php

class Test extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testCheckKey($key)
    {
        $this->load->model("UserModel");
        echo $this->UserModel->checkKey($key) ? "true" : "false";
    }
    
    public function log()
    {
        $this->load->database();
        $this->db->select("value, time");
        $this->db->from("log");
        $this->db->limit(100);
        $this->db->order_by("id", "DESC");
        foreach($this->db->get()->result() as $row)
        {
            echo $row->time . "\t=>\t" . str_replace("\\\"", "\"", $row->value) ."<br /><br />";
        }
    }
}

?>