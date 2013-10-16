<?php

class Member extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("GameModel");
    }

    public function index()
    {
        $this->signUP();
    }
    
    public function signUP()
    {
        $this->load->view('header');
        $this->load->view('Member/signup');
        $this->load->view('footer');
    }
    
    public function login($gKey)
    {
        $data["gKey"] = $gKey;
        $this->load->view('header');
        $this->load->view('Member/login',$data);
        $this->load->view('footer');
    }
}

?>