<?php

class Event extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("ExecModel");
        $this->load->model("AuthModel");
    }

    public function request($cKey)
    {
        $this->checkIsPermit($cKey);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        $this->load->model("RoomModel", "room");
        header("Content-Type: text/event-stream");
        header('Cache-Control: no-cache');
        if (ob_get_level())
            ob_end_flush();

        $this->out->save("Events", $this->out->convertToEvent("Debug", "Hello Request"));
        $this->out->flush();

        $count = 0;
        while (true)
        {
            if ($count++ >= 3)
            {// 過一陣子 要回傳一些訊息給 SSE
                $this->out->save("Events",$this->out->convertToEvent("Debug", "Test Request"));
                $this->out->flush();
                $count = 0;
            }
            
            if ($roomId != 0)
            {
                $events = $this->ExecModel->listen($userId, $roomId, $this->out, $this->room);
                if (count($events) > 0)
                {
                    $this->out->save("Events", $events);
                    $this->out->flush();
                    $count = 0;
                }

            }
            else
            {
                $rooms = $this->room->roomInfo($this->out);
                $this->out->save("Events", array($this->out->convertToEvent("RefreshRoomList", $rooms)));
                $this->out->flush();
                $count = 0;
            }
            sleep(3);
        }
    }

    private function checkIsPermit($cKey)
    {
        $isPermit = $this->AuthModel->checkCommuKey($cKey);
        if (!$isPermit)
        { // 通訊Key 認證失敗
            $this->out->wrong("No Auth Can Request");
        }
    }
}

?>