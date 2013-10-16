<?php

class Developers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("GameModel");
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('developers');
        $this->load->view('footer');
    }
}

?>