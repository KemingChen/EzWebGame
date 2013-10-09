<?php

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
    }

    public function create($name, $account, $password)
    {
        echo $this->UserModel->create($name, $account, $password);
    }
}

?>