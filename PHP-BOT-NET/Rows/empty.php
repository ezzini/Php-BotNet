<?php
include('dba.php');
set_time_limit(5);
function unzipFile($path , $name ){
	$reelPath = $path.DIRECTORY_SEPARATOR.$name;
	$zip = new ZipArchive;
	$res = $zip->open($reelPath);
	if ($res === TRUE) {
		$zip->extractTo($path);
		$zip->close();
		return true ;
	}
	else {
		return false ;
	}
}

function aes128_cbc_encrypt($key, $data, $iv) {
  if(16 !== strlen($key)) $key = hash('MD5', $key, true);
  if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
  $padding = 16 - (strlen($data) % 16);
  $data .= str_repeat(chr($padding), $padding);
  return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

function aes256_cbc_encrypt($key, $data, $iv) {
  if(32 !== strlen($key)) $key = hash('SHA256', $key, true);
  if(16 !== strlen($iv)) $iv = hash('MD5', $iv, true);
  $padding = 16 - (strlen($data) % 16);
  $data .= str_repeat(chr($padding), $padding);
  return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

function ddosudp($ip , $port = 80){
	$starttime = microtime(true);
	while((microtime(true)-$starttime) < 5){
		echo "doing".(microtime(true)-$starttime)."</br>";
	}
}


function zipFile($path , $name ){


	$source = $path ;
	$destination = realpath($path.DIRECTORY_SEPARATOR."..").DIRECTORY_SEPARATOR.$name.".zip";
	 if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

function torTraffic($id  ,$post = NULL, $pathlengh = 3){
    $DBA = new DBA(NULL);
    $bot = $DBA->loadAll( 'bots');
    $flag = 0 ;

    for ($i=0; $i < count($bot) ; $i++) { 
        if($bot[$i]['id'] == $id){
            unset($bot[$i]);
            $flag =1 ;
            break ;
        }
    
    }
    if($flag == 0) return false ;

    
   echo json_encode($bot);


}
torTraffic(2)
?>