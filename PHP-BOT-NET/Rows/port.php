<?php
$host = 'server.ts3enation.com';
$ports = array(21, 25, 80, 30033, 110, 443, 10011);

foreach ($ports as $port)
{
    $fp = @fsockopen($host, $port, $errno, $errstr, 1);
    if(!$fp){
    	echo "port $port is closed </br>" ;

    }
    else {
    	echo "port $port is open </br>" ;
    }
}
?>
    