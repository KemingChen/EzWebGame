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

    /**
     * Exec::start()
     * 
     * �}�l�C��
     * 
     * @param mixed $cKey
     * @return void
     */
    public function start($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->load->model("RoomModel", "room");
        $this->ExecModel->start($userId, $roomId, $this->out, $this->room);
        $this->out->save("Start", true);
        $this->out->show();
    }

    /**
     * Exec::SendMessage()
     * 
     * �C���� �ǰe���O����L���a
     * 
     * @param mixed $message
     * @param mixed $cKey
     * @return void
     */
    public function SendMessage($message, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        // �T�{�ж�
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);

        // �T�{�{�b�O����ۤv�e�T��
        if ($roomInfos[0]["turn"] == $userId)
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
    
    /**
     * Exec::nextRound()
     * 
     * @param mixed $cKey
     * @return void
     */
    public function nextRound($cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        // �T�{�ж�
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->out->delete("Room");// �R���x�s�bout����Room Key
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);
        
        // �T�{�{�b�O����ۤv�e�T��
        if ($roomInfos[0]["turn"] == $userId)
        {
            $playerId = $this->ExecModel->next($roomInfos, $userId, $roomId);
            $this->out->save("NextRound", $playerId);
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }

        $this->out->show();
    }
}

?>