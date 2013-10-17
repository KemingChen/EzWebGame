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

    // �}�l�C��
    public function start($cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->load->model("RoomModel", "room");
        $this->ExecModel->start($userId, $roomId, $this->out, $this->room);
        $this->out->save("Start", true);
        $this->out->show();
    }

    // �C���� �ǰe���O����L���a
    /**
     * Exec::SendMessage()
     * 
     * @param mixed $message
     * @param mixed $cKey
     * @return void
     */
    public function SendMessage($message, $cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        // �T�O���ж��O �C���� �B�s�b
        $this->load->model("RoomModel", "room");
        $room = $this->room->roomInfo($this->out, $roomId, "start");
        if (count($room) <= 0)
        {
            $this->out->wrong("Cannot Send Message To Room");
        }

        // �T�O�{�b�O����ۤv�e�T��
        if ($room[0]["turn"] == $userId)
        {
            $this->ExecModel->send($message, $userId, $roomId);
            $this->out->save("Message", $message);
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }

        $this->out->show();
    }
}

?>