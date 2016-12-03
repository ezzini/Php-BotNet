<?php

	$socks = file_get_contents("sockslist.txt");
	$sock = explode("\n", $socks) ;
	$ports  = array();
	$finalstring =  "";
		foreach ($sock as $sock) {

		$port = explode(":" , $sock) ;
		$port = $port[1];

		if(!in_array($port, $ports)){
		 $ports[] = $port;
		 $finalstring.=$port.",";
		 	
		}
	}
	echo $finalstring;

?>