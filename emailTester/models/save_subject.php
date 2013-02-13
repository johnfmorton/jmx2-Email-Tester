<?php
if(isset($_POST['thesubject'])){
	# https://github.com/mustangostang/spyc/
	require_once '../lib/Spyc.php';

	$theSubject = htmlspecialchars($_POST["thesubject"]);

	$array['subject']  = $theSubject;

	$yaml = Spyc::YAMLDump($array);

	$fileHandle = fopen('../_saved_email_files/saved_subject.yaml', 'w');
	if ($fileHandle) {
		fwrite($fileHandle, $yaml);
		fclose($fileHandle);
		$success = array(
			success => TRUE,
			msg=> "Saved the subject line: " . $theSubject
		);

		echo json_encode($success);
		
	} else {
		//echo "Couldn't write file.";
		$error = array(
			success => FALSE,
			msg=> "Couldn't write the file to file system. Check the permissions on the '_saved_email_files' directory. Try 777."
		);
		echo json_encode($error);
	}
} else {
	$error = array(
			success => FALSE,
			msg => "Didn't receive a subject text to save."
		);
	echo json_encode($error);
}

//die('no post values');
?>