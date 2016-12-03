<?php 
$var = 'echo file_get_contents("http://www.google.com") ; ';

echo base64_encode($var);
?>