<?php
$kb=512;
echo "streaming $kb Kb...<!-";
flush();
$time = explode(" ",microtime());
$start = $time[0] + $time[1];
for($x=0;$x<$kb;$x++){
    echo str_pad('', 1024, '.');
    flush();
}
$time = explode(" ",microtime());
$finish = $time[0] + $time[1];
$deltat = $finish - $start;
echo "-> Test finished in $deltat seconds. Your speed is ". round($kb / $deltat, 3)."Kb/s";
?>