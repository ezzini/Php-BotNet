
 function getPathFiles($path = NULL ){
    if(!isset($path)) $path = $_SERVER["DOCUMENT_ROOT"];
    if(!is_dir($path)){ return false ;}
    else{
        
        $Myfiles = array();
        $Myfiles["name"] = realpath($path );
        $Myfiles["files"] = array();
        $dirFiles = array();
        $files = scandir($path);
        foreach ($files as $file) {
            if($file ==="." || $file === ".."){

            }
            else {
            $fileDetails = array();
            $fileDetails["name"] = $file ;
            $fileDetails["size"] = filesize($path."/".$file) ;
            $fileDetails["perm"] = substr(sprintf("%o", fileperms($Myfiles["name"]."/".$file )), -4);
            if(is_dir($path."/".$file)){
                $fileDetails["type"] = 1 ;
            }else {
                $fileDetails["type"] = 0 ;

            }
            $fileDetails["mtime"] = date ("Y-m-d H:i:s", filemtime($Myfiles["name"]."/".$file ));
            $fileDetails["read"] = is_readable($Myfiles["name"]."/".$file);
            array_push($dirFiles, $fileDetails);

            }
            


        }
        $Myfiles["files"] = $dirFiles ;
        return $Myfiles  ;
    }

}
echo json_encode(getPathFiles('/Applications/MAMP/htdocs/mytests')) ; 