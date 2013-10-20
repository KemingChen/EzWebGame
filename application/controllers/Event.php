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

    public function request($event, $cKey)
    {
        //$nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);

        header("Content-Type: text/event-stream");
        header('Cache-Control: no-cache');
        if (ob_get_level())
            ob_end_flush();
        
        
        
        while (true)
        {
            $events = $this->ExecModel->listen("roomChanged", $userId, $roomId);
            if (count($events) > 0)
            {
                $this->out->save("Events", $events);
                echo "data: ";
                $this->out->show();
                echo "\n\n";
                flush();
            }
            sleep(3);
        }
    }
}

?>