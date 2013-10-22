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
        
        // ����ж����
        $this->load->model("RoomModel", "room");
        $roomInfo = $this->room->roomInfo($this->out, $roomId);
        $this->out->delete("Room");
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        
        $turnPlayer = $this->ExecModel->start($userId, $roomId, $this->out, $roomInfo, $roomPlayers);
        
        // �i����L���a �C���}�l
        $message = sprintf("Room[%d] Start Game", $roomId);
        $this->ExecModel->send("start", $message, $userId, $roomId, $roomPlayers);
        
        // �i���Ҧ����a �{�b����
        $message = json_encode($turnPlayer);
        $this->ExecModel->send("turn", $message, $userId, $roomId, $roomPlayers, true);
        
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

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        $this->ExecModel->send("message", $message, $userId, $roomId, $roomPlayers);
        $this->out->save("Message", $message);

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
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $playerId = $this->ExecModel->next($roomInfo, $userId, $roomId);
        $this->out->save("NextRound", $playerId);

        $this->out->show();
    }
    
    /**
     * Exec::ArriveFinalStep()
     * 
     * �i�D��L���a �A�w�g��� �ç�ۤv�q�^�X���������
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ArriveFinalStep($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        $this->ExecModel->next($roomInfo, $userId, $roomId);
        $this->ExecModel->removeFromPlayingList($userId, $roomInfo);
        $this->out->save("ArriveFinalStep", true);

        $this->out->show();
    }
    
    /**
     * Exec::checkRoomExistAndIsTurnMe()
     * 
     * @param mixed $userId
     * @param mixed $roomId
     * @return �Ǧ^�ثe�ж���T
     */
    private function checkRoomExistAndIsTurnMe($userId, $roomId)
    {
        // �T�{�ж�
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->out->delete("Room"); // �R���x�s�bout����Room Key
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);

        // �T�{�{�b�O����ۤv�e�T��
        if ($roomInfos[0]["turn"] == $userId)
        {
            return $roomInfos[0];
        }
        else
        {
            $this->out->wrong("Game isn't turn me");
        }
    }
}

?>