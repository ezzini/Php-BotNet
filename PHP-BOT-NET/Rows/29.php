<?php

	$socks = file_get_contents("socks.txt");
	$sock = explode("\n", $socks) ;
	$ports  = array();
	$finalstring =  "";
		foreach ($sock as $sock) {
		$data = explode("  " , $sock) ;
		
		$ports = explode("," , $data[1]);
		foreach ($ports as $port) {
			
			$finalstring.=$data[0].":".$port."</br>";
		}
	}
	echo $finalstring;

?>