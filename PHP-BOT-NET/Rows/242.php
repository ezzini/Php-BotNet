<?php

$db = new PDO('mysql:host=localhost;dbname=BECMAROC;charset=utf8mb4', 'root', 'root');

$reponse = $db->query("  SELECT count(*) as 'nbr' , employee_dim.lastName as 'name' from Demande_FactTable , employee_dim WHERE Demande_FactTable.idEmployerVerification = employee_dim.idEmployee group by employee_dim.idEmployee");

$mo = array();
$value = array();

while ($donnees = $reponse->fetch())


{	
	$r = array();
	
	$mo[] =$donnees['name'];
	$value[] = intval($donnees['nbr']);

	/*$value = array();
	$value[] =  intval($donnees['x']);
	$value[] =  intval($donnees['y']);
	$value[] =  intval($donnees['z']);
	//$r['data'] =$value;  
	$mo[]= $value;*/


}

$mo = str_replace('"', "'" , json_encode($mo));
$value = str_replace('"', "'" , json_encode($value));
echo $mo."</br>" ;
echo $value."</br>" ;

?>
