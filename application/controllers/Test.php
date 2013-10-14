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
}

?>