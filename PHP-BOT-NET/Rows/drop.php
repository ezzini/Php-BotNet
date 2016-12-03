<?php
require 'PHP/rb.php';
session_start();
R::setup( 'mysql:host=localhost;dbname=bdsas', 'bdsas', 'bdsasfsdm' );
R::setAutoResolve( TRUE );
$slide = R::dispense('account');
$slide['ok'] = 'sdssd';
R::store($slide);
?>