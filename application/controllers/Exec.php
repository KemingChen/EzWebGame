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
    public function SendMessage($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $message = $this->input->post('message');
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
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        foreach ($roomPlayers as $player)
        {
            if ($player["userId"] == $playerId)
            {
                $this->out->save("NextRound", $player);

                // �i����L���a �{�b����
                $message = json_encode($player);
                $this->ExecModel->send("turn", $message, $userId, $roomId, $roomPlayers);
                break;
            }
        }

        $this->out->show();
    }

    /**
     * Exec::ArriveFinalStep()
     * 
     * ��Ӫ��a ���A�w�g��� �ݥL�P���P�N
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ArriveFinalStep($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        
        //�M��A�X�����a �h�T�{�O�_���H�����C��
        $askPlayer = array();
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        foreach($roomPlayers as $player)
        {
            if($player["userId"] != $userId)
            {
                array_push($askPlayer, $player);
                break;
            }
        }
        
        $this->room->waitCheckWin($roomId, $userId);
        $message = json_encode(array("WinnerId" => $userId));
        $this->ExecModel->send("checkWin", $message, $userId, $roomId, $askPlayer);
        
        $this->out->show();
    }

    /**
     * Exec::Replay()
     * 
     * �^�_ ��軡WIN�����a�O�_���
     * 
     * @param mixed $isWin
     * @param mixed $cKey
     * @return void
     */
    public function Reply($isWin, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
        $this->load->model("RoomModel", "room");
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);
        $winUserId = $this->room->getWaitCheckWinUserId($roomId);
        if($isWin == "true")
        {
            $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
            $roomInfo = $roomInfos[0];
            $playerId = $this->ExecModel->next($roomInfo, $winUserId, $roomId);
            $this->ExecModel->removeFromPlayingList($winUserId, $roomInfo);
            foreach ($roomPlayers as $player)
            {
                if ($player["userId"] == $playerId)
                {
                    $turnWhoMessage = json_encode($player);
                }
                if($player["userId"] == $winUserId)
                {
                    $message = json_encode($player);
                    $this->ExecModel->send("arrived", $message, $userId, $roomId, $roomPlayers, true);
                }
            }
            
            // �⪱�a�s�J winList
            $winList = $this->room->saveToWinList($roomId, $winUserId);
            
            // �T�{���ж��O�_�w�g����
            $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
            $roomInfo = $roomInfos[0];
            if(count(explode("-", $roomInfo["list"])) == 1)
            {
                $rank = explode("-", $winList."-".$roomInfo["list"]);
                foreach ($rank as &$Id)
                {
                    foreach ($roomPlayers as $player)
                    {
                        if ($player["userId"] == $Id)
                        {
                            $Id = $player;
                            break;
                        }
                    }
                }
                
                // ��ж��令wait
                $this->ExecModel->end($roomId);
                
                // �i���Ҧ��H Rank And GameOver
                $this->ExecModel->send("rank", json_encode($rank), $userId, $roomId, $roomPlayers, true);
            }
            else
            {
                // �i���Ҧ����a �{�b����
                $this->ExecModel->send("turn", $turnWhoMessage, $userId, $roomId, $roomPlayers, true);
            }
        }
        else
        {
            $message = "Cheat";
            $this->ExecModel->send("arrived", $message, $userId, $roomId, $roomPlayers, true);
        }
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