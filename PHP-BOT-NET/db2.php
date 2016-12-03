<?php
class DBA{

  private $defaultStorePath  ;
  private $defaultStorePathIndex ;
  private $is_encryptred  ; 
  private $publicKey  ; 
  private $status  ; 

  function __construct($path=NULL , $is_encryptred=false , $key=NULL  ) {

     $this->defaultStorePath = dirname(__FILE__).DIRECTORY_SEPARATOR."DBACACHESTORE" ;
     $this->is_encryptred = false ;
     $this->status = false ; 
     if(isset($path)){
        if(!is_dir($path)){
           $this->status = false ;
           return false ; 
       }
       else {

           $this->defaultStorePath = $path ;
       }	
   }
   else {
 			@mkdir($this->defaultStorePath); //SUPPOSE THAT PHP MKDIR ALWAYS UP
 		}
 		if(isset($is_encryptred ,$key )){
       $this->is_encryptred = true;
 		   $this->publicKey = $key ;   
 		}
 		else {
 			$this->status = true  ; 
 		}


    }
    public function getStats(){
    	
    	$data = array();
    	$data['storePath'] = $this->defaultStorePath ;
    	$data['is_encryptred'] = $this->is_encryptred ;
    	$data['key'] = $this->key ;
    	$data['status'] = $this->status ;
    	return $data ;

    } 
    private function hashObject($object){
    	return sha1(serialize($object)) ;

    }
    private function randKey($lenght = 32){

        $abc = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM.,/!@#$%^&*()_{};:`~";
        $name ="";
        for ($i=0; $i <$lenght ; $i++) { 
            $name.=$abc[rand(0,strlen($abc)-1)];
        }
        return $name;
    }
    private function crypteObject($objectString , $publicKey){
    	
      $key = $this->randKey();
      $iv_size = 16;
      $iv = substr( sha1($key), 0, $iv_size);
      $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key ,$objectString, MCRYPT_MODE_CBC, $iv);
      $ciphertext = $iv . $ciphertext;
      $ciphertext_base64 = base64_encode($ciphertext); 
      $node = array();
      openssl_public_encrypt($key, $crtyp, $publicKey);
      $node['crtyKey'] = base64_encode($crtyp);
      $node['data'] = $ciphertext_base64;
      return json_encode($node);



  }
  private function decrypteObject($objectStringCrypted , $privateKey){

   $node = json_decode($objectStringCrypted , true );
   $node['crtyKey'] =  base64_decode(trim($node['crtyKey']));

   if(!openssl_private_decrypt($node['crtyKey'] , $key, $privateKey ))return false ;
  
   $iv_size = 16;
   $iv = substr( sha1($key), 0, $iv_size);
   $objectStringCrypted = $node['data'];
   $ciphertext_dec = base64_decode($objectStringCrypted);
   $iv_dec = substr($ciphertext_dec, 0, $iv_size);
   $ciphertext_dec = substr($ciphertext_dec, $iv_size);
   $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
   return $plaintext_dec ;


}
public function binarySearch($array , $id , $start , $end ){


  if($array[$start]['id']==$id){
     return $array[$start];
 }
 else if($array[$end]['id']==$id){


     return $array[$end];
 }
 else {

     $midle =  (int)($end+$start)/2;

     if($id == $array[$midle]['id'] ){
        return $array[$midle];

    }
    else if($id > $midle){
        $start = $midle ; 
    }
    else {
        $end = $midle ; 
    }
    return $this->binarySearch($array , $id , $start , $end );

}
}
public function getIndexById($id , $type ){

   $indexFilePath = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR."index";
   $indexFile = file_get_contents($indexFilePath);
   $indexes = json_decode($indexFile , true );
   return $this->binarySearch($indexes , $id , 0 , count($indexes)-1);

}
public function addToIndex($objectLink , $type ){
   $index = array();
   $index['PATH'] = $objectLink ;
   $indexFilePath = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR."index";
   if(!file_exists($indexFilePath)){
    		//NEW TYPE 
      $index['id'] = 1;
      $indexFiles =  array();
      array_push($indexFiles, $index);
      $finalarray =   $indexFiles ;
  }
  else {

      $indexFile = file_get_contents($indexFilePath);
      $indexes = json_decode($indexFile , TRUE );
      $index['id'] = $indexes[count($indexes)-1]['id']+1;
      array_push($indexes, $index );
      $finalarray = $indexes ;


  }
  file_put_contents($indexFilePath , json_encode($finalarray)) ;
  return $index['id'];

}
public function loadAll( $privateKey , $type='DF'){
    $array = array();
    $indexFilePath = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR."index";
    $indexFile = file_get_contents($indexFilePath);
    $indexes = json_decode($indexFile , true );
    foreach ($indexes as $index) {
        $data = $this->load($index['id'] ,   $privateKey  , $type);
        array_push($array, $data);
    }
    return $array ;
}
public function delete($id , $type ='DF'){

      $index =  $this->getIndexById($id , $type);
      if($index == false ) return false ;
        $indexFilePath = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR."index";
        $indexFile = file_get_contents($indexFilePath);
        $indexes = json_decode($indexFile , true );
        for ($i=0; $i < count($indexes) ; $i++) { 
          
          if($indexes[$i]['id']==$id){
            unset($indexes[$i]);
            $data = json_encode($indexes);
            @file_put_contents($indexFilePath , $data) ;
            return true ;
          }

        }
        return false ;

      

}
public function load($id,  $privateKey , $type = 'DF'){
  $index = $this->getIndexById($id , $type);
  if($index==false)return false ;
  else{

  }
   $index = $this->getIndexById($id , $type);
   if($index == false ){
      return false ;
  }
  else {

      $objectPath = $index['PATH'];
      $objectLink = $PATH = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$index['PATH'];
      $objectAsString = file_get_contents($objectLink);
      if($this->is_encryptred){

         $objectAsString = $this->decrypteObject($objectAsString ,  $privateKey );  
     }
     $object = json_decode(trim($objectAsString ), true );
     $object['id']=$id;
     return  $object  ;


 }
}
public function update($id , $object , $type = 'DF'){
    $obj =  $this->getIndexById($id , $type );
    if(isset($obj['PATH'])){
         $objectLink = $PATH = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$obj['PATH'];
         $objectString = json_encode($object );
         if($this->is_encryptred){
            
            $objectString = $this->crypteObject($objectString , $this->publicKey );

        }
        @file_put_contents($objectLink, $objectString);
        return true ;


    }
    else{
        return false ;
    }
} 
public function store($object , $type ='DF'  ) {
 										    // DF reffer TO DEFAULT 
 $string = json_encode($object);
 $PATH = $this->defaultStorePath.DIRECTORY_SEPARATOR.$type;
 
 if(!is_dir($PATH )){
 			@mkdir($PATH); 				//NEW OBJECT TYPE
 		}

 		$hash = $this->hashObject($object).rand(0,10000);
 		$objectLink = $PATH.DIRECTORY_SEPARATOR.$hash ;

 		$objectString = json_encode($object);

 		if($this->is_encryptred){
 			
 			$objectString = $this->crypteObject($objectString , $this->publicKey );
 		}
 		@unlink($objectLink);
 		file_put_contents($objectLink, $objectString);
 		return $this->addToIndex($hash , $type );
 		
 	}

 }  
    /*
    $res=openssl_pkey_new();
    openssl_pkey_export($res, $privkey);
    $pubkey=openssl_pkey_get_details($res);  
    $pubkey  = $pubkey["key"];

    file_put_contents("publicKey", base64_encode($pubkey));
    file_put_contents("privateKey", base64_encode($privkey));

    
    $obj = array();
    $obj['name'] = "CD WORKING FINE";

    $DBA = new DBA(NULL , TRUE ,  $pubkey );
    $ro = $DBA->load(1 , $privateKey );
    echo json_encode($ro);
   // echo json_encode($ro);
   /* $id = $DBA->store($obj);
    echo $id;
    /*echo $id;
    echo  json_encode($obj);
    echo "=====";
    $ro = $DBA->load($id , $privateKey );
    echo json_encode($ro);*/

  //  echo $privateKey."</br>".$pubkey;
    
 ?>