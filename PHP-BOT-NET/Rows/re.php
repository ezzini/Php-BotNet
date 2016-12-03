<?php 
$to = 'sadiqiomar022@gmail.com';
 $subject = 'Test email using PHP';
  $message = 'This is a test email message'; 
  $headers = 'From: no-reply@bdsas.usmba.ac.ma' . "\r\n" ; 
  mail($to, $subject, $message, $headers, '-fwebmaster@example.com'); ?>