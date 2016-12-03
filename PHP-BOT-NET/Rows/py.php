<?
function ping($ip , $port = 80){
    $starttime = microtime(true);
    $file      = @fsockopen ($ip, $port, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file) $status = -1; 
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}

 echo ping(("149.202.110.48",73);
 ?>