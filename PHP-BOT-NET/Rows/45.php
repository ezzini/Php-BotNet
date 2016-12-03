<?php

$action = $_POST['action'];

$db = new PDO('mysql:host=localhost;dbname=BECMAROC;charset=utf8mb4', 'root', 'root');

	$reponse = $db->query("SELECT count(*) as 'nbr' , Date.month as 'month' FROM Demande_FactTable , Date WHERE Demande_FactTable.idDate = Date.id AND idBEC = 3010102 GROUP BY Date.month  ");


	$return = array();
	while ($donnees = $reponse->fetch()){


		// # filtrage des donneeés
		
		if($donnees['month']==="0"){
		
		}
		else{
			$return[] =  intval($donnees['nbr']);
		}
		


	}
	echo json_encode($return);

?>