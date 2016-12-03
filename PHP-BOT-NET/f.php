<?
include('functions.php');
include('db2.php') ;
include('commandeLib.php') ;

$file = file_get_contents("CACHEDBA.cfg");
$file = base64_decode(trim($file));
echo $file;
?>
