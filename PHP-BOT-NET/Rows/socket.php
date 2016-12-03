<?php
$ip = long2ip(rand(0, "4294967295"));
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$ip_dest = "149.202.110.48";
socket_bind($sock, $ip);
socket_connect($sock, $ip_dest, 42);
socket_close($sock);

?>