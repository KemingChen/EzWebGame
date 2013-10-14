<?php

class Template extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function loadView($headerActiveID, $slideBarList, $content, $contentData)
    {
        $this->loadHeader($headerActiveID);
        if($slideBarList!=null)
        {
            $this->loadSlideBarContent($slideBarList, $content, $contentData);
        }
        else
        {
            $this->load->view($content, $contentData);
        }
        $this->loadFooter();
    }

    private function loadHeader($activeID)
    {
        $isLogin = $this->authority->isLogin();
        $list = $this->MenuModel->getHeaderList();
        $this->updateActive($list, $activeID);
        $data = array('list' => $list);
        $data["isLogin"] = $isLogin;
        $data["username"] = $this->authority->getName();
        $this->load->view("include/header", $data);
    }

    private function updateActive(&$data, $activeID)
    {
        foreach ($data as & $item)
        {
            $item["Active"] = $item["ID"] == $activeID ? "active" : "";
        }
    }

    private function loadSlideBarContent($slideBarList, $content, $contentData)
    {
        $slideBarData = array('list' => $slideBarList);
        $this->load->view("include/slideBarContentHeader", $slideBarData);
        $this->load->view($content, $contentData);
        $this->load->view("include/slideBarContentFooter");
    }

    private function loadFooter()
    {
        $this->load->view('include/Footer');

    }
}

?>