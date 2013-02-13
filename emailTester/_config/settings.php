<?php 
$email_config['smtp']            =  "smtp.gmail.com";
// http://swiftmailer.org/docs/sending.html
// You can use SSL or TLS encryption 
// If you are using Gmail as your sender, you will need "ssl"
$email_config['encryption_type'] =  "ssl";
// If you are not using encryption, the port is probably "25".
// If you are using SSL "465" or "587" will probably be your port number.
// Gmail uses port "465".
$email_config['port']            =  "465";
// This is your username for SMTP. If using Gmail, this is your full email 
// address for example, john@gmail.com. If you use Google Apps for Domains,  
// this is your full email address, for example "john@mydomain.com"
$email_config['smtpusername']    =  "username@gmail.com";
// The name you want to appear in the email sender name area.
$email_config['senderrealname']  =  "Gmail Email Test Robot";
// Your SMTP password. If you are using Gmail with 2 factor authenication
// this will need to be an application specific password instead of your
// "master" SMPT password. See https://accounts.google.com/b/2/IssuedAuthSubTokens#accesscodes
$email_config['smtppassword']    =  "mysecretpassword";