<?php

class SSEtest extends CI_Controller 
{
	public function index()
	{
        header("Content-Type: text/event-stream");  
        header('Cache-Control: no-cache');
        if (ob_get_level()) ob_end_flush();;
        
        $count = 0;  
        while($count<5) {  
            echo "data: " . "Server Sent Event round: " . $count . "\n\n";  
            $count++;  
            flush();  
            sleep(1);
        }
	}
}
?>