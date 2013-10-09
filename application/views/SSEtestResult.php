<?php  
header("Content-Type: text/event-stream");  
flush();  
$count = 0;  
while($count<5) {  
    echo "data: " . "Server Sent Event round: " . $count . "\n\n";  
    $count++;  
    flush();  
    sleep(1);
}