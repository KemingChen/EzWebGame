<?php

class SSEtest extends CI_Controller 
{
	public function index()
	{
		//$this->load->view('SSEtestResult');
        header("Content-Type: text/event-stream");  
        header('Cache-Control: no-cache');
        flush();  
        $count = 0;  
        while($count<5) {  
            echo "data: " . "Server Sent Event round: " . $count . "\n\n";  
            $count++;  
            flush();  
            sleep(1);
        }
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */