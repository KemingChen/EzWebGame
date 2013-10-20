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

    public function login($lKey = null)
    {
        $this->load->library('user_agent');
        $data["lKey"] = $lKey;
        $this->load->view('header');
        if ($this->agent->is_referral())
        {
            $this->load->view('Member/login', $data);
        }
        $this->load->view('footer');
    }
}

?>