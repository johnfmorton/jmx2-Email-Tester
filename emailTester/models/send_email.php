<?php 
error_log('in send_meail.php');
if(isset($_POST['theFileName'])){
	# For sending email
	require_once '../lib/swift_required.php';

	# Get config info for SMTP email
	require_once '../_config/settings.php';

	function isValidEmail($email){
	    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);
	}

	function getSavedHTMLFile($filename) {
		$path = "../_saved_email_files"; 

		if ( $filename != '') {
			$contents=file_get_contents("{$path}/{$filename}"); 
			if ($contents === false) 
			{ 
				return '';
			} else { 
				return $contents; 
			} 
		} else { 
			return ''; 
		}
	}

	$theSubject = htmlspecialchars($_POST["theSubject"]);

	# Format the recipient list in a format appropriate for SwiftMailer
	$recipientsArray = json_decode($_POST["theRecipients"]);
	$recipientsForSwiftMailer = array();
	for ($i = 0; $i < count($recipientsArray); $i++){
		$useremail = $recipientsArray[$i]->useremail;
		$username = $recipientsArray[$i]->username !== '' ? $recipientsArray[$i]->username : null;
		if (isValidEmail($useremail)){
			$recipientsForSwiftMailer[$useremail] = $username;
		}
	}

	$the_email_body = getSavedHTMLFile(htmlspecialchars($_POST["theFileName"]));

	$encryption_type = $email_config['encryption_type'] ? $email_config['encryption_type'] : null;

	// Create the Transport
	// NOTE: If you use Google 2-factor authentication, 
	// you will need to include a application specfic password
	// not your real password. 
	$transport = Swift_SmtpTransport::newInstance($email_config['smtp'] , $email_config['port'], $encryption_type)
	  ->setUsername($email_config['smtpusername'])
	  ->setPassword($email_config['smtppassword'])
	  ;

	// $transport = Swift_MailTransport::newInstance();
	// Create the Mailer using your created Transport
	$mailer = Swift_Mailer::newInstance($transport);

	$message = Swift_Message::newInstance();
	$message->setFrom(array($email_config['smtpusername'] => $email_config['senderrealname']));

	// to whom?
	$message->setTo($recipientsForSwiftMailer);

	date_default_timezone_set('America/New_York');
	$message->setSubject($theSubject);
	$message->setBody($the_email_body, 'text/html');
	$message->addPart('This is plain text content. Fill with appropriate content.', 'text/plain');

	// Send the message
	//$result = $mailer->send($message);
	error_log('sending message now.');
	try {
		$result = $mailer->send($message);
		$success = array(
				'success' => $result,
				'msg'=> "Sent email: " . $theSubject
			);

		echo json_encode($success);
	}
	catch (Exception $e)
	{
		error_log( 'Message: ' . $e->getMessage() );
		$success = array(
				'success' => FALSE,
				'msg'=> $e->getMessage()
			);

		echo json_encode($success);
	}

} else {
	$success = array(
			'success' => FALSE,
			'msg'=> "No filename received to send."
		);

	echo json_encode($success);
}