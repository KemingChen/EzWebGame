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
     * 開始遊戲
     * 
     * @param mixed $cKey
     * @return void
     */
    public function start($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        // 獲取房間資料
        $this->load->model("RoomModel", "room");
        $roomInfo = $this->room->roomInfo($this->out, $roomId);
        $this->out->delete("Room");
        $roomPlayers = $this->room->playerInfo($roomId, $this->out);

        $turnPlayer = $this->ExecModel->start($userId, $roomId, $this->out, $roomInfo, $roomPlayers);

        // 告知其他玩家 遊戲開始
        $message = sprintf("Room[%d] Start Game", $roomId);
        $this->ExecModel->send("start", $message, $userId, $roomId, $roomPlayers);

        // 告知所有玩家 現在換誰
        $message = json_encode($turnPlayer);
        $this->ExecModel->send("turn", $message, $userId, $roomId, $roomPlayers, true);

        $this->out->save("Start", true);
        $this->out->show();
    }

    /**
     * Exec::SendMessage()
     * 
     * 遊戲中 傳送指令給其他玩家
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

                // 告知其他玩家 現在換誰
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
     * 找個玩家 說你已經獲勝 看他同不同意
     * 
     * @param mixed $cKey
     * @return void
     */
    public function ArriveFinalStep($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $roomInfo = $this->checkRoomExistAndIsTurnMe($userId, $roomId);
        
        //尋找適合的玩家 去確認是否此人完成遊戲
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
     * 回復 剛剛說WIN的玩家是否獲勝
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
            
            // 把玩家存入 winList
            $winList = $this->room->saveToWinList($roomId, $winUserId);
            
            // 確認此房間是否已經結束
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
                
                // 把房間改成wait
                $this->ExecModel->end($roomId);
                
                // 告知所有人 Rank And GameOver
                $this->ExecModel->send("rank", json_encode($rank), $userId, $roomId, $roomPlayers, true);
            }
            else
            {
                // 告知所有玩家 現在換誰
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
     * @return 傳回目前房間資訊
     */
    private function checkRoomExistAndIsTurnMe($userId, $roomId)
    {
        // 確認房間
        $this->load->model("RoomModel", "room");
        $roomInfos = $this->room->roomInfo($this->out, $roomId, "start");
        $this->out->delete("Room"); // 刪除儲存在out中的Room Key
        $this->ExecModel->checkRoomIsStart($roomInfos, $this->out);

        // 確認現在是輪到自己送訊息
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