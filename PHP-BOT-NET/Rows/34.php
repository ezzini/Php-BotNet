<?
function getDomaineIp($email){
	$s = explode('@', $email);
	if(count($s)!=2) return false;
	else { 
		$hosts = array();
		getmxrr($s[1], $hosts);
		if(count($hosts)==0) return false ;
		foreach ($hosts as $host) {
			return $host;
			$ip = gethostbyname($host);
			if(filter_var($ip, FILTER_VALIDATE_IP) && checkWorkServer($ip)){

				return $ip;
			}
		}
		return false;
	}
}
function checkWorkServer($ip){
	$socket = @fsockopen($host, 25, $errno, $errstr, 10);
	if(!$socket){return false ;}
	return true;
}
$data = 'yshiran@iil.intel.com
ytm@ytmltd.com
ytraining@aol.com
yudhvirsangha@hotmail.com
yuenho@yuenho.com';
$email = "khil3@hotmail.com";
$domaine = explode("@", $email)[1];
$hosts = array();
getmxrr($domaine, $hosts);
foreach ($hosts as $host) {
	echo $email."--".$host."</br>";
}
?>
