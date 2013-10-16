<?php

class Exec extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("ExecModel");
        $this->load->model("AuthModel");
    }
    
    // ¶}©l¹CÀ¸
    public function start($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->load->model("RoomModel", "room");
        $this->ExecModel->start($userId, $roomId, $this->out, $this->room);
        $this->out->save("Start", true);
        $this->out->show();
    }
}

?>